#include "server_com.h"
#include "main_mqtt.h"
#include "devdata.h"
#include "ssl_certs.h"
#include "debug.h"
#include "helpers.h"
#include "devcom.h"
#include "config.h"
#include <ESP8266WiFi.h>

// server communication, DEVICE option setting / confirming / error functions

void servcom_send_option_update(const char *opt_name, const char *opt_value, const uint16_t opt_val_len, uint8_t run_mqtt_loop) // run_mqtt_loop should be 0 if run from mqtt callback
{
    char topic[50] = "";
    // normal set requests
    main_mqtt_prepare_header(topic);
    main_mqtt_add_device_header(topic);
    strcat(topic, "/");
    strcat(topic, opt_name);
    main_mqtt.publish(topic, (uint8_t *)opt_value, opt_val_len, 0);
    if (run_mqtt_loop)
        main_mqtt.loop();
}

void servcom_option_change_successful(const char *opt_name)
{
    char topic[50] = "";
    main_mqtt_prepare_header(topic);
    main_mqtt_add_device_header(topic);
    strcat(topic, "/");
    strcat(topic, opt_name);
    strcat(topic, "/set");

    main_mqtt.publish(topic, (uint8_t *)"", 0, 1);
}
void servcom_option_confirm_singleshot(const char *opt_name)
{
    char topic[50] = "";
    main_mqtt_prepare_header(topic);
    main_mqtt_add_device_header(topic);
    strcat(topic, "/");
    strcat(topic, opt_name);
    strcat(topic, "/singleshot");
    main_mqtt.publish(topic, (uint8_t *)"ok", 2, 0);
}
void servcom_option_cant_set(const char *opt_name)
{
    char topic[50] = "";
    main_mqtt_prepare_header(topic);
    main_mqtt_add_device_header(topic);
    strcat(topic, "/");
    strcat(topic, opt_name);
    strcat(topic, "/error");
    main_mqtt.publish(topic, (uint8_t *)"cant_set", 8, 0);
}
// server communication, modem configuration setting / confirming / error functions

void servcom_send_module_stats(void)
{
    static uint32_t last_send_time = 0;
    static uint8_t first_send = 0;
    if (millis() - last_send_time > 30000)
    {
        last_send_time = millis();

        // things to send
        char buf[20];
        servcom_modem_option_update("state", "up", 2, 1);
        // milliseconds from restart
        itoa(millis(), buf, 10);
        servcom_modem_option_update("ms_from_rst", buf, strlen(buf), 1);
        servcom_modem_option_update("net_name", WiFi.SSID().c_str(), strlen(WiFi.SSID().c_str()), 1);
        // wifi signal rssi
        int32_t rssi_val = WiFi.RSSI();
        if (rssi_val != 31) // rssi value 31 means error code
        {
            itoa(WiFi.RSSI(), buf, 10);
            servcom_modem_option_update("rssi", buf, strlen(buf), 1);
        }
        if (first_send == 0)
        {
            first_send = 1;
            servcom_modem_send_all_options();
        }
        // send basic rs-485 stats
        itoa(devcom_rx_analyzer[DEVCOM_RXANALYZER_R_CNT], buf, 10);
        servcom_modem_option_update("rs_r_cnt", buf, strlen(buf), 1);

        itoa(devcom_rx_analyzer[DEVCOM_RXANALYZER_NO_ASCI_CNT], buf, 10);
        servcom_modem_option_update("rs_invchar_cnt", buf, strlen(buf), 1);

        itoa(devcom_rx_analyzer[DEVCOM_RXANALYZER_BYTE_CNT], buf, 10);
        servcom_modem_option_update("rs_byte_cnt", buf, strlen(buf), 1);

        itoa(devcom_rx_analyzer[DEVCOM_RXANALYZER_BRACKET_CNT], buf, 10);
        servcom_modem_option_update("rs_bracket_cnt", buf, strlen(buf), 1);
    }
}

void servcom_modem_option_update(const char *name, const char *value, const unsigned int value_len, uint8_t run_mqtt_loop) // run_mqtt_loop should be 0 if run from mqtt callback
{
    char topic[50] = "";
    main_mqtt_prepare_header(topic);
    strcat(topic, "mod/");
    strcat(topic, name);
    main_mqtt.publish(topic, (uint8_t *)value, value_len, 0);
    if (run_mqtt_loop)
        main_mqtt.loop();
}
int8_t servcom_modem_process_exec(const char *fn, const char *params, const unsigned int params_length)
{
    yield();

    if (memcmp(fn, "refresh", strlen("refresh")) == 0)
    {
        println_dbg("doing option refresh");
        devdata_flags |= DEVDATA_FLAG_SEND_ALL_OPTS;
        return 0;
    }
    if (memcmp(fn, "dbg_tx_on_r", strlen("dbg_tx_on_r")) == 0)
    {
        if (strlen(params) >= 100)
            return -2;
        strcpy((char *)devcom_set_msg, params);
        devcom_flags |= DEVCOM_FLAGS_SET_MSG_READY_FOR_TX;
        return 0;
    }
    if (memcmp(fn, "dbg_tx", strlen("dbg_tx")) == 0)
    {
        devcom_dev_tx(params);
        return 0;
    }
    else if (memcmp(fn, "fm_url", strlen("fm_url")) == 0)
    {
        return config_update_firmware(params);
    }
    else if (memcmp(fn, "fm_upver", strlen("fm_upver")) == 0)
    {
        return config_update_firmware_by_ver(params);
    }
    return -1; // unknown exec command
}

void servcom_modem_send_all_options(void)
{
    char value_str[15];
    // send device pooling period
    itoa(config_opts[dev_pool_period], value_str, 10);
    servcom_modem_option_update("dev_pool_per", value_str, strlen(value_str), 1);
    delay(10);
    // send debug mode value
    itoa(config_opts[debug_level], value_str, 10);
    servcom_modem_option_update("dbg_lvl", value_str, strlen(value_str), 1);
    delay(10);
    // send firmware version
    servcom_modem_option_update("fw_ver", FIRMWARE_VERSION, strlen(FIRMWARE_VERSION), 1);
    delay(10);
    // send dname if set
    if (config_dname[0] != 0)
        servcom_modem_option_update("dname", config_dname, strlen(config_dname), 1);
    delay(10);
}
int8_t servcom_modem_set_option(const char *opt, const char *value, const unsigned int value_length)
{
    print_dbg("servcom_modem_set_option opt:");
    print_dbg(opt);
    print_dbg(" value:");
    println_dbg(value);
    if (memcmp(opt, "dev_pool_per", strlen("dev_pool_per")) == 0)
    {
        config_save_option(dev_pool_period, value);
        servcom_modem_confirm_option("dev_pool_per");
        servcom_modem_option_update("dev_pool_per", value, strlen(value), 0);
    }
    else if (memcmp(opt, "dbg_lvl", strlen("dbg_lvl")) == 0)
    {
        config_save_option(debug_level, value);
        servcom_modem_confirm_option("dbg_lvl");
        servcom_modem_option_update("dbg_lvl", value, strlen(value), 0);
    }
    return 0;
}
void servcom_modem_confirm_option(const char *opt)
{
    char topic[50] = "";
    main_mqtt_prepare_header(topic);
    strcat(topic, "mod/");
    strcat(topic, opt);
    strcat(topic, "/set");
    main_mqtt.publish(topic, (uint8_t *)"", 0, 1);
    // main_mqtt.loop();
}

// processing mqtt messages from server

int8_t servcom_process_device_options(const char *part_topic, const char *value)
{
    uint8_t clist_opt_flags = 0;

    int16_t opt_name_end = findchar((uint8_t *)(part_topic), 5, '/');
    if (opt_name_end == -1 || opt_name_end >= 5)
        return -3;
    if (strncmp(part_topic + opt_name_end + 1, "set", 3) != 0)
    {
        if (strncmp(part_topic + opt_name_end + 1, "singleshot", 10) == 0)
        {
            clist_opt_flags |= DEVDATA_OPTFLAG_SINGLE_SHOT;
        }
        else // didnt match anything, exit
        {
            return -4;
        }
    }
    char opt_name[5];
    memcpy(opt_name, part_topic, opt_name_end);
    opt_name[opt_name_end] = 0;

    if (strcmp(opt_name, "MULT") == 0)
    {
        if (value[0] == 0)
            return 0;
        devdata_save_coin_mult(atoi(value));
        servcom_option_change_successful("MULT");
        servcom_send_option_update("MULT", value, strlen(value), 1);
        delay(20);
        return 0;
    }

    print_dbg("set_req for opt:");
    println_dbg(opt_name);
    if (value[0] == 0)
    {
        devdata_clist_clear_opt(opt_name);
        print_dbg("clearing set_req");
        return 0;
    }
    if (strlen(value) < 25)
    {
        devdata_clist_add(opt_name, value, clist_opt_flags);
        print_dbg("setting opt to val:");
        println_dbg(value);
        return 0;
    }
    return -5;
}

int8_t servcom_process_modem_options(const char *part_topic, const char *value, const unsigned int value_length)
{
    if (value[0] == 0)
        return 0; // ignore empty values
    int16_t opt_name_end = findchar((uint8_t *)(part_topic), 15, '/');
    if (opt_name_end == -1 || opt_name_end >= 15)
        return -3;
    if (strncmp(part_topic + opt_name_end + 1, "set", 3) == 0)
    {
        return servcom_modem_set_option(part_topic, value, value_length); // process modem option set here
    }
    if (strncmp(part_topic + opt_name_end + 1, "exec", 4) == 0)
    {
        return servcom_modem_process_exec(part_topic, value, value_length); // process functions execution here, params are in 'value'
    }
    return -4; // not valid format
}

int8_t servcom_process_msg_from_srv(const char *topic, const char *value, const unsigned int value_length)
{

    char topic_cmp[40] = "";
    main_mqtt_prepare_header(topic_cmp);
    // check if message is really addressed to us
    if (memcmp(topic, topic_cmp, strlen(topic_cmp)) != 0)
        return -1; // message not addressed to us, return
    // check if we received modem options
    if (memcmp(topic + strlen(topic_cmp), "mod/", 4) == 0)
    {
        return servcom_process_modem_options(topic + strlen(topic_cmp) + 4, value, value_length);
    }

    // check if we received device options
    main_mqtt_add_device_header(topic_cmp); // add device header
    strcat(topic_cmp, "/");
    if (memcmp(topic, topic_cmp, strlen(topic_cmp)) == 0)
    {
        // device options
        return servcom_process_device_options(topic + strlen(topic_cmp), value);
    }
    return -22;
}
