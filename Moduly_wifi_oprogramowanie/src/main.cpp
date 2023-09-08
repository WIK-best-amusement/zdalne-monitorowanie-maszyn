#include <FS.h>
#include <ESP8266WiFi.h>
#include <WiFiClientSecure.h>
#include <PubSubClient.h>
#include <time.h>
// wifi manager includes
#include <DNSServer.h>
#include <ESP8266WebServer.h>
#include <WiFiManager.h>
// project includes
#include "config.h"
#include "debug.h"
#include "main_mqtt.h"
#include "board_defs.h"
#include "devcom.h"
#include "devdata.h"
#include "tests.h"
#include "server_com.h"

WiFiManager wifiManager;
static long last_mqtt_server_conn_time_ms = 150000;

// initializes IO and blinks both LEDs for 5 seconds at program start
void init_io(void)
{
  // init button
  pinMode(BUTTON_PIN, INPUT_PULLUP);
  // rs485 switch to rx
  pinMode(RS485_TX_EN_PIN, OUTPUT);
  digitalWrite(RS485_TX_EN_PIN, LOW);
  // set leds as outputs
  pinMode(LED_RED_PIN, OUTPUT);
  pinMode(LED_GREEN_PIN, OUTPUT);
  digitalWrite(LED_GREEN_PIN, HIGH);
  digitalWrite(LED_RED_PIN, HIGH);
  delay(2500);
  digitalWrite(LED_GREEN_PIN, LOW);
  digitalWrite(LED_RED_PIN, LOW);
}
void setup()
{
  init_io();
  Serial.begin(9600);

  tests_before_config();

  if (!SPIFFS.begin()) // begin will try formating fs if it doesnt exist
  {
    println_dbg("spiffs mount fail");
  }

  // load configuration from SPIFFS
  config_load_from_spiffs();
  devdata_coin_mult = config_opts[2];

  // initialize device communication
  devcom_init();
  devdata_clist_init();

  // init time
  yield();
  configTime(1 * 3600, 0, "0.pool.ntp.org", "1.pool.ntp.org", "2.pool.ntp.org");
  yield();

  tests_after_init();
  println_dbg("init end");
}

// pool button state and starts wifi configuration(by SmartConfig or WifiManager portal) if pressed for long enough
void check_for_button(void)
{
  uint16_t but_cnt = 0;
  while (digitalRead(BUTTON_PIN) == LOW)
  {
    but_cnt++;
    delay(100);
    if (but_cnt > 40) // use wifi manager if pressed for more than 10 seconds
    {
      digitalWrite(LED_GREEN_PIN, (but_cnt + 1) % 2);
      digitalWrite(LED_RED_PIN, but_cnt % 2);
    }
  }
  if (but_cnt > 40)
  {
    devcom_rx_ticker_stop();
    digitalWrite(LED_RED_PIN, HIGH);
    digitalWrite(LED_GREEN_PIN, LOW);
    // start wifi manager for 10 minutes max
    WiFiManager wifiManager;
    wifiManager.setTimeout(600);
    char net_name[25] = "net_config_";
    itoa(ESP.getChipId(), net_name + 11, 16); // add chipid in hex to the end of the config wifi network name

    WiFiManagerParameter device_name("device_name", "device name", "", 19);
    WiFiManagerParameter client_number("client_num", "client number", "", 9);
    wifiManager.addParameter(&device_name);
    wifiManager.addParameter(&client_number);

    if (wifiManager.startConfigPortal(net_name, "") == true) // if successful
    {
      if (client_number.getValue()[0] != 0)
        config_save_group_hash(client_number.getValue());
      if (device_name.getValue()[0] != 0)
        config_save_dname(device_name.getValue());
    }
    delay(1000);
    ESP.reset();
    delay(10000);
  }
}

void loop()
{
  check_for_button();

  // check fr invalid wifi mode (only allow WIFI_STA)
  if (WiFi.getMode() != WIFI_STA)
  {
    WiFi.mode(WIFI_STA);
    yield();
  };

  // check for wifi connection
  if (WiFi.status() != WL_CONNECTED)
  {
    digitalWrite(LED_GREEN_PIN, LOW); // GREEN led off on no wifi
    digitalWrite(LED_RED_PIN, LOW);   // red led off on no mqtt connection

    devcom_rx_ticker_stop(); // stop receiving data from device when there is no wifi connection
    println_dbg("wr");
    config_wifi_try_all_with_pass("wikwifi123");
    delay(100);
    last_mqtt_server_conn_time_ms = millis(); // reset no mqtt connection timeout timer (so if we finally connect to wifi after long disconnect time, we dont disconnect immidately)
  }
  else
  {
    digitalWrite(LED_GREEN_PIN, HIGH); // GREEN led on on wifi ok

    if (!main_mqtt.connected())
    {
      devcom_rx_ticker_stop();
      digitalWrite(LED_RED_PIN, LOW); // red led off on no mqtt connection
      println_dbg("main_mqtt..");
      main_mqtt_connect();
      if (config_wifi_persistance_state == false && millis() - last_mqtt_server_conn_time_ms > 180000) // if no mqtt connection for 10 minutes, but wifi is connected
      {
        WiFi.disconnect();                        // disconnect so that we try connecting to different network
        last_mqtt_server_conn_time_ms = millis(); // reset no mqtt connection timeout timer
        println_dbg("dis wifi mqtt timeout");
      }
    }
    else
    {
      digitalWrite(LED_RED_PIN, HIGH); // red led on mqtt ok
      devcom_process();
      devdata_periodic_send();
      servcom_send_module_stats();
      main_mqtt.loop();                         // has to be done every a while
      last_mqtt_server_conn_time_ms = millis(); // reset no mqtt connection timeout timer (because we are conencted to mqtt server and everything is fine)
    }
  }
  delay(50); // add some delay when we are connected to server
}