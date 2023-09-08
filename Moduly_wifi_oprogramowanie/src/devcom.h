#ifndef _DEVCOM_H_
#define _DEVCOM_H_
#include <ESP8266WiFi.h>

#define DEVCOM_RX_ANALYZER_NUM 4
extern uint32_t devcom_rx_analyzer[DEVCOM_RX_ANALYZER_NUM];
#define DEVCOM_RXANALYZER_R_CNT 0
#define DEVCOM_RXANALYZER_NO_ASCI_CNT 1
#define DEVCOM_RXANALYZER_BYTE_CNT 2
#define DEVCOM_RXANALYZER_BRACKET_CNT 3

#define DEVCOM_MSG_BUFFER_LEN 1024

extern volatile unsigned char devcom_set_msg[100]; // test only

extern volatile uint8_t devcom_flags;
#define DEVCOM_FLAGS_SET_MSG_READY_FOR_TX (1 << 0)

// RTAG states are used for detecting "<R>" that is used for sending data back to device
enum devcom_state
{
    WAITING,
    RTAG1,
    RTAG2,
    RTAG3,
    READING_DEVICE_DATA
};

void devcom_init(void);
void devcom_process(void);
void devcom_clear_buffer(void);
void devcom_tx_ticker_cb();
void devcom_rx_ticker_cb();
void devcom_rx_ticker_start(void);
void devcom_rx_ticker_stop(void);
void devcom_dev_tx(const char *data);
void devcom_rx_serial_analyzer(const char data);

#endif