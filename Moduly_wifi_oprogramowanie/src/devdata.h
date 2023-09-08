#include <ESP8266WiFi.h>
#include "config.h"

struct devdata_opt_changelist
{
    char opt[4];   // it's four so that we have one byte for \0 termination
    char value[4]; // options are always 4 byte (maybe sometimes they could be 2 byte long in special cases like time)
    uint8_t retry_count;
    uint8_t flags;
};
#define DEVDATA_CLIST_LEN 100

// devdata_opt_changelist.flags
#define DEVDATA_OPTFLAG_ALREADY_SENT (1 << 0) // already sent in this iteration
#define DEVDATA_OPTFLAG_SINGLE_SHOT (1 << 1)  // send only once
#define DEVDATA_OPTFLAG_SET_FAILED (1 << 2)   // send only once

extern devdata_opt_changelist devdata_clist[DEVDATA_CLIST_LEN];

extern uint16_t devdata_coin_mult;

extern volatile unsigned char devdata_flags;
#define DEVDATA_FLAG_SEND_ALL_OPTS (1 << 0)

void devdata_parse_device_string(const unsigned char *str, unsigned short len, uint8_t buffer_num);

void devdata_save_coin_mult(uint16_t value);

void devdata_clist_init(void);
int16_t devdata_clist_find(const char *opt_name);                                                  // returns position in array if found. If not found returns -1
int16_t devdata_clist_find_empty(void);                                                            // returns position in array if found. If not found returns -1
int16_t devdata_clist_add(const char *opt_name, const char *requested_value, const uint8_t flags); // returns pos if added, -1 if clist was full , if opt_name is already in array, updates it
void devdata_clist_check_value(const char *opt_name, const char *device_value);
void devdata_clist_clear_pos(int16_t pos);
void devdata_get_change_string(char *str, u16 max_len);
void devdata_clist_clear_opt(const char *opt_name);

void devdata_periodic_send(void);
