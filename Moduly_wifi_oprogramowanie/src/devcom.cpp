#include "devcom.h"
#include "devdata.h"
#include "config.h"
#include "board_defs.h"
#include "debug.h"
#include "server_com.h"
#include <Arduino.h>
#include <Ticker.h>

volatile unsigned char message_buffer[DEVCOM_MSG_BUFFER_LEN];
volatile short message_length = 0;
Ticker devcom_tx_ticker;
Ticker devcom_rx_ticker;

volatile unsigned char devcom_set_msg[100] = "";
volatile uint8_t devcom_flags;
volatile uint8_t devcom_expected_data_type = 0; // devcom_requested_data_type gets saved here on end of data frame rx
volatile uint8_t devcom_requested_data_type = 0;

uint32_t devcom_rx_analyzer[DEVCOM_RX_ANALYZER_NUM];

void devcom_init(void)
{
    Serial.begin(9600);
    Serial.setRxBufferSize(1024);
    pinMode(RS485_TX_EN_PIN, OUTPUT); // rs485 select to low
    digitalWrite(RS485_TX_EN_PIN, LOW);
    devcom_flags = 0;
}
// starts periodic task that checks for data waiting on UART every 5ms, it's done because we can't have to big delays in uart data procesing
void devcom_rx_ticker_start(void)
{
    if (devcom_rx_ticker.active() == false)
    {
        while (Serial.available()) // flushes rx queue
        {
            Serial.read();
        }
        devcom_rx_ticker.attach_ms(5, devcom_rx_ticker_cb);
    }
}
// stops periodic task that checks for data waiting on UART every 5ms
void devcom_rx_ticker_stop(void)
{
    if (devcom_rx_ticker.active() == true)
        devcom_rx_ticker.detach();
}
// transfer data on rs-485 bus, includes toggling rs485 rx enable pin, and waiting for all data to be sent
void devcom_dev_tx(const char *data)
{
    Serial.flush();
    digitalWrite(RS485_TX_EN_PIN, HIGH);
    Serial.println(data);
    Serial.flush();
    digitalWrite(RS485_TX_EN_PIN, LOW);
}
// used for sending data to the mainboard in short time windows where it releases bus for us (after we get '<R>' tag)
void devcom_tx_ticker_cb(void)
{
    if (devcom_flags & DEVCOM_FLAGS_SET_MSG_READY_FOR_TX)
    {
        devcom_dev_tx((char *)devcom_set_msg);
        devcom_set_msg[0] = 0;
        devcom_flags &= ~DEVCOM_FLAGS_SET_MSG_READY_FOR_TX;
    }
    else
    {
        static uint32_t last_ask_time = millis();
        if ((millis() - last_ask_time > config_opts[dev_pool_period]) && message_length == 0)
        {
            if (devcom_requested_data_type == 0)
            {
                devcom_dev_tx("{?}");
                devcom_requested_data_type = 1;
            }
            else
            {
                devcom_dev_tx("{@}");
                devcom_requested_data_type = 0;
            }
            last_ask_time = millis();
        }
    }
}
// used for doing some additional rs-485 data analysis, could be used to eg. detect broken frames(because of bad cable connections/power supply)
void devcom_rx_serial_standard_analyzer(const char data)
{
    devcom_rx_analyzer[DEVCOM_RXANALYZER_BYTE_CNT]++; // counts total bytes recevied
    if (data < 32 && data != '\n' && data != '\r')
        devcom_rx_analyzer[DEVCOM_RXANALYZER_NO_ASCI_CNT]++;
    if (data == '{' || data == '(' || data == '<' || data == '[')
        devcom_rx_analyzer[DEVCOM_RXANALYZER_BRACKET_CNT]++;
    if (data == '}' || data == ')' || data == '>' || data == ']')
        devcom_rx_analyzer[DEVCOM_RXANALYZER_BRACKET_CNT]--;
}
void devcom_rx_serial_advanced_analyzer(const char data)
{

}
// state machine that is run every 5ms on new UART data( if there is any waiting)
void devcom_rx_ticker_cb(void)
{
    static enum devcom_state state = WAITING;
    static short buffer_pos;

    while (Serial.available())
    {
        char data = Serial.read();
        devcom_rx_serial_standard_analyzer(data);
        if (config_opts[debug_level] > 0)
            devcom_rx_serial_advanced_analyzer(data);
        if (state == WAITING && data == '<') // waiting for starting tag
        {
            state = RTAG1;
        }
        else if (state == RTAG1)
        {
            if (data == 'R')
                state = RTAG2;
            else if ((data == '0' || data == 'G') && message_length == 0) // if valid start of data frame and buffer is empty ( valid start is char '0' or 'G' )
            {
                message_buffer[0] = data;
                buffer_pos = 1;
                state = READING_DEVICE_DATA;
            }
            else
                state = WAITING;
        }
        else if (state == READING_DEVICE_DATA)
        {
            if (data != '>' && buffer_pos < DEVCOM_MSG_BUFFER_LEN)
            {
                // ignore message if it has invalid characters inside
                if (data < 32 || data > 122 || data == '<' || data == '[' || data == ']' || data == '(' || data == ')')
                {
                    message_length = 0; // set_message_length to 0 when we start saving new message(so that the message to could have been left there gets marked as no longer valid)
                    state = WAITING;    // reset state back to waiting.. maybe we could start counting those events? maybe even detect some rs485 errors this way?
                }
                else
                {
                    message_buffer[buffer_pos] = data;
                    buffer_pos++;
                }
            }
            else // end of message
            {
                if (buffer_pos > 20 && data == '>')
                    message_length = buffer_pos; // this marks data in message_buffer as ready for processing(it gets parsed in devcom_process) if it's longer than 20 chars
                devcom_expected_data_type = devcom_requested_data_type;
                state = WAITING;
            }
        }
        else if (state == RTAG2 && data == '>')
        {
            devcom_rx_analyzer[DEVCOM_RXANALYZER_R_CNT]++;
            devcom_tx_ticker.once_ms(20, devcom_tx_ticker_cb);
        }
        else
            state = WAITING;
    }
}
// periodically called from main loop, used to process received messages from mainboard or all rs-485 activity if in debug mode 3+
void devcom_process(void)
{
    if (config_opts[debug_level] < 2) // this is normal mode
    {
        devcom_rx_ticker_start();
        if (message_length > 0)
        {
            devdata_parse_device_string((unsigned char *)message_buffer, message_length, devcom_expected_data_type);
            devdata_get_change_string((char *)devcom_set_msg, 100);
            devcom_flags |= DEVCOM_FLAGS_SET_MSG_READY_FOR_TX;
            message_length = 0;
        }
    }
    else
    {
        devcom_rx_ticker_stop();
        uint8_t i = 1;
        char buffer[128];
        while (i != 0)
        {
            i = 0;
            while (Serial.available() && i < 128)
            {
                buffer[i] = Serial.read();
                i++;
            }
            if (i > 0)
            {
                servcom_modem_option_update("dbg_string", buffer, i, 1);
            }
            yield();
        }
    }
}