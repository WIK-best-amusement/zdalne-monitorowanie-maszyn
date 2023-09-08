#include <WiFiClientSecure.h>

extern char mqtt_ident[11];
extern const char ca_cert[];
extern const char client_cert[];
extern const char client_key[];

extern BearSSL::X509List ca_crt;
extern BearSSL::X509List client_crt;
extern BearSSL::PrivateKey priv_key;

void sslcert_init();
