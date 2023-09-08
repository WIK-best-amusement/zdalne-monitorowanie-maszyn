#ifndef _CONFIG_H_
#define _CONFIG_H_
#include <ESP8266WiFi.h>

// wifi config
#define WIFI_DEFAULT_SSID "default_wifi_network"
#define WIFI_DEFAULT_PASSWORD "default_password"
// rs485 communication config
#define RS485_BAUDRATE 9600
// firmware version
#define FIRMWARE_VERSION "38"
// mqtt server configuration
#define MAIN_MQTT_SERVER_ADDRESS "online.wik.pl"
#define MAIN_MQTT_SERVER_PORT 8883

#define CONFIG_OPTIONS_COUNT 3
extern volatile uint32_t config_opts[CONFIG_OPTIONS_COUNT];
enum config_option_names_t
{
    dev_pool_period = 0,
    debug_level = 1,
    devdata_coin_multt = 2
};

extern bool config_wifi_persistance_state;

extern char config_ssid[30];
extern char config_password[30];
extern char config_mqtt_server_addr[30];
extern char config_group_hash[10];
extern char config_device_serial[10];
extern char config_device_type[4];
extern char config_dname[20];

int8_t config_load_from_spiffs(void);
void config_save_group_hash(const char *grp_hash_str);
void config_save_option(config_option_names_t opt, const char *value);

void config_wifi_try_all_with_pass(const char *password);

int8_t config_update_firmware(const char *fm_url);
int8_t config_update_firmware_by_ver(const char *ver);

void config_save_dname(const char *dname_str);

#endif //_CONFIG_H_