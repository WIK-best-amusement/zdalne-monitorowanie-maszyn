#include <WiFiClientSecure.h>
#include <PubSubClient.h>
#include "main_mqtt.h"
#include <FS.h>
#include "config.h"
#include "devcom.h"
#include "debug.h"
#include "ssl_certs.h"
#include "server_com.h"

BearSSL::WiFiClientSecure wifi_secure_mqttclient;
PubSubClient main_mqtt(config_mqtt_server_addr, MAIN_MQTT_SERVER_PORT, main_mqtt_rx_callback, wifi_secure_mqttclient);

void main_mqtt_check_mfln(void)
{
  wifi_secure_mqttclient.setBufferSizes(2048, 2048);
}
void main_mqtt_prepare_header(char *buf)
{
  buf[0] = 0;
  strcat(buf, mqtt_ident);
  strcat(buf, "/");
  strcat(buf, config_group_hash);
  strcat(buf, "/");
}
void main_mqtt_add_device_header(char *buf)
{
  strcat(buf, config_device_type);
  strcat(buf, ".");
  strcat(buf, config_device_serial);
}
int8_t main_mqtt_connect(void)
{

  if (!main_mqtt.connected())
  {
    // prepare topic for last will
    char topic[50] = "";
    main_mqtt_prepare_header(topic);
    strcat(topic, "mod/state");
    delay(1000);
    main_mqtt_check_mfln();
    wifi_secure_mqttclient.setTrustAnchors(&ca_crt);
    wifi_secure_mqttclient.setClientRSACert(&client_crt, &priv_key);
    if (main_mqtt.connect("", topic, 1, true, "down"))
    {
      main_mqtt.publish(topic, (uint8_t *)"up", 2, 1); // publish status message to id/mod/state
      main_mqtt.loop();
      print_dbg("main_mqtt ok");
      main_mqtt_subscribe_modem_topics();
      main_mqtt.loop();
      if (config_device_serial[0] && config_device_type[0])
      {
        main_mqtt_subscribe_dev_topics();
      }
      main_mqtt.loop();
    }
    else
    {
      print_dbg("main_mqtt fail,rc=");
      println_dbg(main_mqtt.state());

      // SSL only
      print_dbg("ssl=");
      println_dbg(wifi_secure_mqttclient.getLastSSLError());
    }
  }
  return main_mqtt.connected();
}
void main_mqtt_subscribe_modem_topics()
{
  char topic[50] = "";
  // normal set requests
  main_mqtt_prepare_header(topic);
  strcat(topic, "mod/+/set");
  main_mqtt.subscribe(topic, 1);
  // exec requests
  topic[0] = 0;
  main_mqtt_prepare_header(topic);
  strcat(topic, "mod/+/exec");
  main_mqtt.subscribe(topic, 1);
}

void main_mqtt_subscribe_dev_topics() // can only be run after device serial is known
{
  char topic[50] = "";
  // normal set requests
  main_mqtt_prepare_header(topic);
  main_mqtt_add_device_header(topic);
  strcat(topic, "/+/set");
  main_mqtt.subscribe(topic, 1);
  // single shot requests
  topic[0] = 0;
  main_mqtt_prepare_header(topic);
  main_mqtt_add_device_header(topic);
  strcat(topic, "/+/singleshot");
  main_mqtt.subscribe(topic, 1);
}

void main_mqtt_rx_callback(char *topic, byte *payload, unsigned int length)
{
  uint8_t topic_len = strlen(topic);
  char topic_cpy[121] = "";
  char value_cpy[121] = "";
  if (topic_len < 120 && length < 120)
  {
    strcpy(topic_cpy, topic);
    memcpy(value_cpy, (char *)payload, length);
    value_cpy[length] = 0; // terminate string
    servcom_process_msg_from_srv(topic_cpy, value_cpy, length);
  }
  else
  {
    println_dbg("main_mqtt_rx_to_long!");
  }
}