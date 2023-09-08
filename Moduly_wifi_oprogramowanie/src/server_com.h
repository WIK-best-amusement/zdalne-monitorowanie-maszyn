#include <ESP8266WiFi.h>

int8_t servcom_process_msg_from_srv(const char *topic, const char *value, const unsigned int value_length);

// device option
void servcom_send_option_update(const char *opt_name, const char *opt_value, const uint16_t opt_val_len, uint8_t run_mqtt_loop); // run_mqtt_loop should be 0 if run from mqtt callback
void servcom_option_change_successful(const char *opt_name);
void servcom_option_confirm_singleshot(const char *opt_name);
void servcom_option_cant_set(const char *opt_name);

// modem options
void servcom_modem_option_update(const char *name, const char *value, const unsigned int value_len, uint8_t run_mqtt_loop); // run_mqtt_loop should be 0 if run from mqtt callback
void servcom_modem_confirm_option(const char *opt);
void servcom_modem_send_all_options(void);
void servcom_send_module_stats(void);
