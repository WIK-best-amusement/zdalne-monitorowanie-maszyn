#include "config.h"
#include "debug.h"
#include "main_mqtt.h"
#include <FS.h>
#include "board_defs.h"
#include "ssl_certs.h"
#include "ESP8266httpUpdate.h"

volatile uint32_t config_opts[CONFIG_OPTIONS_COUNT] = {10000, 0, 100};

// WiFi Credentials
char config_ssid[30] = WIFI_DEFAULT_SSID;
char config_password[30] = WIFI_DEFAULT_PASSWORD;
// mqtt server address
char config_mqtt_server_addr[30] = MAIN_MQTT_SERVER_ADDRESS;
// group hash used to add device to the user
char config_group_hash[10] = "0";
char config_dname[20] = "";
// device serial that is read out from device
char config_device_serial[10] = "";
char config_device_type[4] = "";
bool config_wifi_persistance_state = true;

int8_t config_update_firmware(const char *fm_url)
{
  println_dbg("FIRMWARE UPDATE START");
  WiFiClient client;
  ESPhttpUpdate.setLedPin(LED_RED_PIN, HIGH);
  t_httpUpdate_return ret = ESPhttpUpdate.update(fm_url, 17384, "/0lg3rvxaegd45.txt", FIRMWARE_VERSION);
  switch (ret)
  {
  case HTTP_UPDATE_FAILED:
    printf_dbg("HTTP_UPDATE_FAILD Error (%d): %s\n", ESPhttpUpdate.getLastError(), ESPhttpUpdate.getLastErrorString().c_str());
    return -1;
    break;

  case HTTP_UPDATE_NO_UPDATES:
    println_dbg("HTTP_UPDATE_NO_UPDATES");
    return 0;
    break;
  case HTTP_UPDATE_OK:
    println_dbg("HTTP_UPDATE_OK");
    return 0;
    break;
  }
  return -2;
}
int8_t config_update_firmware_by_ver(const char *ver)
{
  println_dbg("FIRMWARE UPDATE START");
  WiFiClient client;
  ESPhttpUpdate.setLedPin(LED_RED_PIN, HIGH);
  t_httpUpdate_return ret = ESPhttpUpdate.update(MAIN_MQTT_SERVER_ADDRESS, 17384, ver, FIRMWARE_VERSION);
  switch (ret)
  {
  case HTTP_UPDATE_FAILED:
    printf_dbg("HTTP_UPDATE_FAILD Error (%d): %s\n", ESPhttpUpdate.getLastError(), ESPhttpUpdate.getLastErrorString().c_str());
    return -1;
    break;

  case HTTP_UPDATE_NO_UPDATES:
    println_dbg("HTTP_UPDATE_NO_UPDATES");
    return 0;
    break;
  case HTTP_UPDATE_OK:
    println_dbg("HTTP_UPDATE_OK");
    return 0;
    break;
  }
  return -2;
}

void config_save_group_hash(const char *grp_hash_str)
{
  File grp_hash_file = SPIFFS.open("/grp_hash.cfg", "w");
  if (grp_hash_file)
  {
    grp_hash_file.println(grp_hash_str);
    grp_hash_file.close();
    strcpy(config_group_hash, grp_hash_str);
  }
}
void config_save_dname(const char *dname_str)
{
  File dname_file = SPIFFS.open("/dname.cfg", "w");
  if (dname_file)
  {
    dname_file.println(dname_str);
    dname_file.close();
    strcpy(config_dname, dname_str);
  }
}

void config_load_options(void)
{
  for (uint16_t i = 0; i < CONFIG_OPTIONS_COUNT; i++)
  {
    char opt_file_str[15] = "/";
    itoa(i, opt_file_str + 1, 10);
    strcat(opt_file_str, ".cfg");
    File opt_file = SPIFFS.open(opt_file_str, "r");
    if (opt_file)
    {
      config_opts[i] = opt_file.parseInt();
      opt_file.close();
    }
  }
}
void config_save_option(config_option_names_t opt, const char *value)
{
  config_opts[opt] = atoi(value); // save value in options array

  // save value in file
  char opt_file_str[15] = "/";
  itoa(opt, opt_file_str + 1, 10);
  strcat(opt_file_str, ".cfg");
  File opt_file = SPIFFS.open(opt_file_str, "w");
  opt_file.print(value);
  opt_file.close();
}

int8_t config_load_from_spiffs(void)
{

  // check for first run
  if (!SPIFFS.exists("/first_run_done.txt"))
  {
    WiFi.mode(WIFI_STA);
    WiFi.begin(config_ssid, config_password);
    delay(1000);
    println_dbg("first run");
    File first_run = SPIFFS.open("/first_run_done.txt", "w");
    first_run.close();
  }

  // read ssl configuration
  sslcert_init();

  // read config hash
  File grp_hash_file = SPIFFS.open("/grp_hash.cfg", "r");
  if (grp_hash_file)
  {
    uint8_t len = grp_hash_file.readBytesUntil('\n', config_group_hash, 9);
    config_group_hash[len] = 0;
    for (uint8_t i = 0; i < len; i++)
    {
      if (config_group_hash[i] < '0' || config_group_hash[i] > '9')
        config_group_hash[i] = 0;
    }
    if (config_group_hash[0] == 0)
      strcpy(config_group_hash, "00000");
    grp_hash_file.close();
  }
  else
  {
    println_dbg("no grp_hash.cfg");
  }
  // read config dname
  grp_hash_file = SPIFFS.open("/dname.cfg", "r");
  if (grp_hash_file)
  {
    uint8_t len = grp_hash_file.readBytesUntil('\n', config_dname, 19);
    config_dname[len] = 0;
    grp_hash_file.close();
  }

  // load other config options from spiffs
  config_load_options();

  return 0;
}

void config_wifi_try_all_with_pass(const char *password)
{
  delay(50);
  WiFi.persistent(false); // dont save things that we are planing to do
  config_wifi_persistance_state = false;
  static char saved_ssid[31] = "";
  static char saved_pass[31] = "";
  if (saved_ssid[0] == 0)
  {
    strcpy(saved_ssid, WiFi.SSID().c_str());
    strcpy(saved_pass, WiFi.psk().c_str());
  }
  print_dbg("sSSID: ");
  println_dbg(saved_ssid);
  print_dbg("spass: ");
  println_dbg(saved_pass);
  delay(200);
  int numberOfNetworks = WiFi.scanNetworks();
  print_dbg("nm:");
  println_dbg(numberOfNetworks);
  delay(200);
  for (int i = 0; i < numberOfNetworks; i++)
  {
    print_dbg("Netname: ");
    println_dbg(WiFi.SSID(i));
    print_dbg("Sig str: ");
    println_dbg(WiFi.RSSI(i));
    delay(150);
    WiFi.begin(WiFi.SSID(i), password);
    delay(150);
    for (uint16_t a = 0; a < 100; a++)
    {
      if (!WiFi.isConnected())
      {
        print_dbg(".");
      }
      else
      {
        // it worked, we got connected to something
        println_dbg("conn ok");
        return;
      }
      delay(75);
      if (digitalRead(BUTTON_PIN) == LOW)
      {
        i = numberOfNetworks;
        break;
      } // exits both inner and outher loop,we want to exit from here fast, when button is pressed
    }   //*/
    println_dbg("-----");
  }
  // connect back to originally saved network if we had no success connecting to any networks with that const pass
  WiFi.begin(saved_ssid, saved_pass);
  for (uint16_t a = 0; a < 100; a++)
  {
    if (!WiFi.isConnected())
    {
      print_dbg("|");
      delay(100);
      if (digitalRead(BUTTON_PIN) == LOW)
        break; // exit this function fast when button is pressed
    }
    else
      break;
  }
}