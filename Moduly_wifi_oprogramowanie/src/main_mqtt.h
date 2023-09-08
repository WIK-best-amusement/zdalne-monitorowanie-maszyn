#include <ESP8266WiFi.h>
#include <PubSubClient.h>

extern PubSubClient main_mqtt;

void main_mqtt_prepare_header(char *buf);
void main_mqtt_add_device_header(char *buf);
void main_mqtt_init();
int8_t main_mqtt_connect(void);
void main_mqtt_rx_callback(char *topic, byte *payload, unsigned int length);
void main_mqtt_subscribe_dev_topics();
void main_mqtt_subscribe_modem_topics();