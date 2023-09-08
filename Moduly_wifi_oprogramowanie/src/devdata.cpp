#include "devdata.h"
#include "devcom.h"
#include "helpers.h"
#include "config.h"
#include "string.h"
#include "ssl_certs.h"
#include "main_mqtt.h"
#include "debug.h"
#include "server_com.h"

#define DEVCOM_MSG_BUFFER_COUNT 2

volatile unsigned char devdata_flags = 0;
unsigned char last_message[DEVCOM_MSG_BUFFER_COUNT][DEVCOM_MSG_BUFFER_LEN] = {"", ""};
short last_message_length[DEVCOM_MSG_BUFFER_COUNT] = {0, 0};

uint16_t devdata_coin_mult = -1;
int64_t devdata_coin_coin_counter = -1;
int32_t devdata_ticket_counter = -1;
int64_t devdata_coin_coin_counter_last_sent = -1;
int32_t devdata_ticket_counter_last_sent = -1;

devdata_opt_changelist devdata_clist[DEVDATA_CLIST_LEN]; // keeps changes to do that we received from MQTT broker

char *devdata_find_last_value(const char *opt)
{
    char temp_opt_str[15];
    if (strlen(opt) > 10)
        return NULL;
    strcpy(temp_opt_str, opt);
    strcat(temp_opt_str, "=");
    char *opt_ptr = strstr((char *)last_message[0], temp_opt_str); // check first buffer
    if (opt_ptr == NULL)
        opt_ptr = strstr((char *)last_message[1], temp_opt_str); // check second buffer
    if (opt_ptr == NULL)                                         // if not found, try with ':' instead
    {
        strcpy(temp_opt_str, opt);
        strcat(temp_opt_str, ":");
        opt_ptr = strstr((char *)last_message[0], temp_opt_str); // check first buffer
        if (opt_ptr == NULL)
            opt_ptr = strstr((char *)last_message[1], temp_opt_str); // check second buffer
    }
    if (opt_ptr == NULL)
        return NULL; // return NULL if option string not found at all
    return opt_ptr + strlen(temp_opt_str);
}

void devdata_parse_device_string(const unsigned char *str, unsigned short len, uint8_t buffer_num)
{
    short opt_start;
    short opt_end = -1; // this must be -1 so that on first iteration we start with opt_start==0
    short deli_pos;

    if (buffer_num >= DEVCOM_MSG_BUFFER_COUNT)
        return; // buffer_num should be 0 or 1

    if (devdata_flags & DEVDATA_FLAG_SEND_ALL_OPTS)
    {
        devdata_flags &= ~DEVDATA_FLAG_SEND_ALL_OPTS;
        // clear last message data so that all the options get sent
        for (uint8_t i = 0; i < DEVCOM_MSG_BUFFER_COUNT; i++)
        {
            last_message[i][0] = 0;
            last_message_length[i] = 0;
        }
    }

    if (len > 5 && memcmp((char *)str, "000=", 4) == 0)
        opt_end += 4; // skip "000=" at the begining of the device string if it exists

    while (true)
    {
        opt_start = opt_end + 1; // start after the last options end
        // first find the end of the next option
        opt_end = findchar(str + opt_start, len - opt_start, ';');
        if (opt_end == -1)
            opt_end = len; // if option delimeter not found, this might be the last option so set opt_end to end of string pos
        else
            opt_end += opt_start; // add position from which we started search(we dont add it earlier because of -1 check)

        // find option name/value delimeter
        deli_pos = findchar(str + opt_start, opt_end - opt_start, '='); // look only in between opt_start and opt_end
        if (deli_pos == -1)
            deli_pos = findchar(str + opt_start, opt_end - opt_start, ':'); // if '=' not found, look for ':'
        if (deli_pos == -1)
            continue;          // if no delimeter found, skip this option
        deli_pos += opt_start; // add position from which we started search(we dont add it earlier because of -1 check)

        // we should have everything we need now
        // option_name is at opt_start, with length==opt_start-deli_pos
        // option_value is at deli_pos+1 with length==opt_end-deli_pos-1
        // prepare topic header here
        char opt_name[7] = "";
        char opt_value[30] = "";
        if ((opt_start - deli_pos < 5) && (opt_end - deli_pos - 1 < 30))
        {
            // copy opt name
            memcpy(opt_name, str + opt_start, deli_pos - opt_start);
            opt_name[deli_pos - opt_start] = 0;
            // copy opt value
            memcpy(opt_value, str + deli_pos + 1, opt_end - deli_pos - 1);
            opt_value[opt_end - deli_pos - 1] = 0;
            // check if that option was in changelist, and delete it from it if it has good value
            devdata_clist_check_value(opt_name, opt_value);
            // check if the value changed compared to the last one
            char *last_opt_val = devdata_find_last_value(opt_name);
            if (last_opt_val != NULL && memcmp(opt_value, last_opt_val, opt_end - deli_pos - 1) == 0)
            {
                // print_dbg(" = ");//----------------------------------------dbg------------------------------------------------
                // option has not changed do idk what
            }
            else // option did change, publish new value
            {
                // print_dbg(" ! ");//----------------------------------------dbg------------------------------------------------
                // save device serial when we find it
                if (config_device_serial[0] == 0 && opt_name[0] == 'S' && opt_name[1] == 0 && strlen(opt_value) < 9)
                {
                    strcpy(config_device_serial, opt_value);
                    if (config_device_serial[0] && config_device_type[0])
                        main_mqtt_subscribe_dev_topics();
                    devdata_flags |= DEVDATA_FLAG_SEND_ALL_OPTS;
                }
                // save device type when we find it, and it isn't already saved
                if (config_device_type[0] == 0 && opt_name[0] == '0' && opt_name[1] == '8' && opt_name[2] == '0' && opt_name[3] == 0 && strlen(opt_value) < 4)
                {
                    strcpy(config_device_type, opt_value);
                    if (config_device_serial[0] && config_device_type[0])
                        main_mqtt_subscribe_dev_topics();
                    devdata_flags |= DEVDATA_FLAG_SEND_ALL_OPTS;
                }

                // detect 002 coin counter
                if (opt_name[0] == '0' && opt_name[1] == '0' && opt_name[2] == '2' && opt_name[3] == 0)
                {
                    devdata_coin_coin_counter = (int64_t)atoi(opt_value) * devdata_coin_mult;
                }
                // detect 083 ticket counter
                if (opt_name[0] == '0' && opt_name[1] == '8' && opt_name[2] == '3' && opt_name[3] == 0)
                {
                    devdata_ticket_counter = atoi(opt_value);
                }

                if (config_device_serial[0] && config_device_type[0])
                    servcom_send_option_update(opt_name, opt_value, opt_end - deli_pos - 1, 1);
                delay(20);
            }
        }

        // stop if we are at the end of the device string
        if (opt_end >= len - 1)
            break;
    }

    // copy just parsed message to last_message ONLY if we already got devices serial number
    if (config_device_serial[0] && config_device_type[0])
    {
        memcpy(last_message[buffer_num], str, len);
        last_message_length[buffer_num] = len;
    }
}

void devdata_send_COIN(void)
{
    char buf[25];
    ultoa(devdata_coin_coin_counter_last_sent, buf, 10);
    servcom_send_option_update("COIN", buf, strlen(buf), 1);
    delay(20);
}
void devdata_send_TICK(void)
{
    char buf[12];
    itoa(devdata_ticket_counter, buf, 10);
    servcom_send_option_update("TICK", buf, strlen(buf), 1);
    delay(20);
}
void devdata_send_COTI(void)
{
    char buf[40];
    ultoa(devdata_coin_coin_counter_last_sent, buf, 10);
    uint8_t pos = strlen(buf);
    buf[pos] = ';';
    utoa(devdata_ticket_counter, buf + pos + 1, 10);
    servcom_send_option_update("COTI", buf, strlen(buf), 1);
    delay(20);
}
void devdata_send_MULT(void)
{
    char buf[12];
    itoa(devdata_coin_mult, buf, 10);
    servcom_send_option_update("MULT", buf, strlen(buf), 1);
    delay(20);
}
void devdata_save_coin_mult(uint16_t value)
{
    devdata_coin_mult = value;
    char buf[15];
    itoa(devdata_coin_mult, buf, 10);
    config_save_option(devdata_coin_multt, buf);
}
void devdata_periodic_send(void)
{
    if (config_device_serial[0] && config_device_type[0])
    {
        // if we didnt receive ticket count in the first 5 minutes set it to 0
        uint8_t send_coti = 0;
        if (devdata_ticket_counter == -1 && millis() > 300000)
        {
            devdata_ticket_counter = 0;
            devdata_ticket_counter_last_sent = 0;
        }
        if (devdata_coin_coin_counter != devdata_coin_coin_counter_last_sent) // if coin_coin counter changed
        {
            devdata_coin_coin_counter_last_sent = devdata_coin_coin_counter;
            send_coti = 1;
            // send COIN
            devdata_send_COIN();
        }
        if (devdata_ticket_counter != devdata_ticket_counter_last_sent) // if ticket counter changed
        {
            devdata_ticket_counter_last_sent = devdata_ticket_counter;
            send_coti = 1;
            // send TICK
            devdata_send_TICK();
        }
        // if coin and ticket counters received at least once from device, and at least one of them got changed
        if ((devdata_coin_coin_counter != -1 && devdata_ticket_counter != -1) && send_coti /*( (devdata_coin_coin_counter!=devdata_coin_coin_counter_last_sent) || (devdata_ticket_counter!=devdata_ticket_counter_last_sent) )*/)
        {
            devdata_coin_coin_counter_last_sent = devdata_coin_coin_counter;
            devdata_ticket_counter_last_sent = devdata_ticket_counter;
            // send COTI
            devdata_send_COTI();
        }

        static unsigned long last_send_time = 0;
        if ((millis() - last_send_time) > 900000) // every 15 minutes
        {
            last_send_time = millis();
            if (devdata_coin_coin_counter != -1)
                devdata_send_COIN();
            if (devdata_ticket_counter != -1)
                devdata_send_TICK();
            if (devdata_coin_coin_counter != -1 && devdata_ticket_counter != -1)
                devdata_send_COTI();
            devdata_send_MULT();
        }
    }
}

void devdata_clist_init(void)
{
    for (int i = 0; i < DEVDATA_CLIST_LEN; i++)
    {
        devdata_clist[i].opt[0] = 0;
    }
}

int16_t devdata_clist_find(const char *opt_name) // returns position in array if found. If not found returns -1
{
    for (int i = 0; i < DEVDATA_CLIST_LEN; i++)
    {
        if (devdata_clist[i].opt[0] && strcmp(devdata_clist[i].opt, opt_name) == 0)
        {
            return i;
        }
    }
    return -1;
}

int16_t devdata_clist_find_empty(void) // returns position in array if found. If not found returns -1
{
    for (int i = 0; i < DEVDATA_CLIST_LEN; i++)
    {
        if (devdata_clist[i].opt[0] == 0)
        {
            return i;
        }
    }
    return -1;
}

int16_t devdata_clist_add(const char *opt_name, const char *requested_value, const uint8_t flags) // returns pos if added, -1 if clist was full , if opt_name is already in array, updates it
{
    int16_t pos = devdata_clist_find(opt_name);
    println_dbg("adding opt");
    if (pos == -1)
    {
        println_dbg("opt didnt exist");
        pos = devdata_clist_find_empty();
        if (pos == -1)
            return -1;
        println_dbg("creating new opt");
        strcpy(devdata_clist[pos].opt, opt_name);
        devdata_clist[pos].flags = flags;
        devdata_clist[pos].retry_count = 0;
    }
    strcpy(devdata_clist[pos].value, requested_value);
    println_dbg("set req success");
    return pos;
}

void devdata_clist_clear_pos(int16_t pos)
{
    devdata_clist[pos].opt[0] = 0;
}
void devdata_clist_clear_opt(const char *opt_name)
{
    int16_t pos = devdata_clist_find(opt_name);
    if (pos != -1)
        devdata_clist_clear_pos(pos);
}

void devdata_clist_check_value(const char *opt_name, const char *device_value)
{
    int16_t pos = devdata_clist_find(opt_name);
    if (pos == -1)
        return;
    println_dbg("found opt in clist");
    uint8_t len = strlen(devdata_clist[pos].value);
    // we have to compare them by values not by strings
    char dev_val[5];
    memcpy(dev_val, device_value, len); // we are doing it because device value is not null terminated
    dev_val[len] = 0;
    int32_t dev_int = atoi(dev_val);
    int32_t req_int = atoi(devdata_clist[pos].value);

    // if(memcmp(devdata_clist[pos].value,device_value,len)==0) //compare by strings
    if (dev_int == req_int) // compare by values
    {
        devdata_clist_clear_pos(pos);
        servcom_option_change_successful(opt_name); // delete retained message from mqtt server as well
        print_dbg("opt changed to good val, deleting ");
        println_dbg(opt_name);
    }
    else
    {
        devdata_clist[pos].flags &= ~DEVDATA_OPTFLAG_ALREADY_SENT;
        print_dbg("opt still bad ");
        println_dbg(opt_name);
    }
}

void devdata_get_change_string(char *str, u16 max_len)
{
    uint16_t opt_cnt = 0;

    str[0] = 0; // clear string
    strcat(str, "{");

    for (uint16_t i = 0; i < DEVDATA_CLIST_LEN && opt_cnt < 1; i++)
    {

        if (devdata_clist[i].opt[0] != 0 && !(devdata_clist[i].flags & DEVDATA_OPTFLAG_ALREADY_SENT))
        {
            // add option seperator if it's not first option being added
            if (opt_cnt > 0)
            {
                strcat(str, ";");
            }

            if (devdata_clist[i].opt[0] != 'G')
                strcat(str, "!");
            if (devdata_clist[i].opt[0] >= '0' && devdata_clist[i].opt[0] <= '9') // options starting with number need 'W' after '!'
            {
                strcat(str, "W");
            }
            strcat(str, devdata_clist[i].opt);
            strcat(str, "=");
            strcat(str, devdata_clist[i].value);

            // mark option as already sent and increase number of options already in string
            opt_cnt++;
            devdata_clist[i].flags |= DEVDATA_OPTFLAG_ALREADY_SENT;
            devdata_clist[i].retry_count++;
            if (devdata_clist[i].retry_count > 15)
            {
                servcom_option_cant_set(devdata_clist[i].opt);
                // servcom_option_confirm_singleshot(devdata_clist[i].opt);
                devdata_clist_clear_pos(i);
            }
            if (devdata_clist[i].flags & DEVDATA_OPTFLAG_SINGLE_SHOT)
            {
                // clearing singleshot option
                servcom_option_confirm_singleshot(devdata_clist[i].opt);
                devdata_clist_clear_pos(i);
            }
        }
    }
    strcat(str, "}");
    if (opt_cnt == 0)
        str[0] = 0; // clear string if no options saved
}
