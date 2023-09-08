#include <ESP8266WiFi.h>
#include "ssl_certs.h"
#include "debug.h"
#include <FS.h>

const char mqtt_ident_p[11] PROGMEM = "1\0   "; // this is based on identity in ssl certificate

const char ca_cert[] PROGMEM = R"EOF(
-----BEGIN CERTIFICATE-----
MIID2zCCAsOgAwIBAgIJAIFwXydmijjCMA0GCSqGSIb3DQEBCwUAMIGDMQswCQYD
VQQGEwJQTDEOMAwGA1UECAwFU2xhc2sxETAPBgNVBAcMCERlYm93aWVjMQwwCgYD
VQQKDANXSUsxFDASBgNVBAsMC2VuZ2luZWVyaW5nMQ8wDQYDVQQDDAZ3aWsucGwx
HDAaBgkqhkiG9w0BCQEWDWx1a2FzekB3aWsucGwwHhcNMTkwMzI3MTQzMDUyWhcN
NDkwMzE5MTQzMDUyWjCBgzELMAkGA1UEBhMCUEwxDjAMBgNVBAgMBVNsYXNrMREw
DwYDVQQHDAhEZWJvd2llYzEMMAoGA1UECgwDV0lLMRQwEgYDVQQLDAtlbmdpbmVl
cmluZzEPMA0GA1UEAwwGd2lrLnBsMRwwGgYJKoZIhvcNAQkBFg1sdWthc3pAd2lr
LnBsMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAwi7G5QHb6aIuk7G/
zKvTlekfrqtF+tvnxcTLWmuqPfMHIfIwDuhAFAneYTXbcfDw9/7OJ+EG34LR8m++
E2TGsv5iA4se6VmZ5Jt+tfAPpQGuNWA9m2sXgTi/o6eQgQ9onDESuACW/3oeEgVc
shIQ74XAhZ9GeSujImeqimgcc5Ful9dEoWYCkhon31RaikAeNPyB929TT5Dt3F1t
cJahXvuHv8Puq29I4HN4cCSq0HEGEgV3prJY2eYz4VLEo5LUmj67bizZ8RJ8FjJV
jm/2qK+HEO8I1ekSf5SZKpLmk2RbBUidEWgcqUM1Ym1maFX3C4OBvOT2hDKmd7M2
Dn3IZwIDAQABo1AwTjAdBgNVHQ4EFgQUcoJ9EEGebxFrarrBDyC2kC252nYwHwYD
VR0jBBgwFoAUcoJ9EEGebxFrarrBDyC2kC252nYwDAYDVR0TBAUwAwEB/zANBgkq
hkiG9w0BAQsFAAOCAQEAhvJPFbaT/k4lQ/X8JMMbQx7Ml3LJe6RkokGl+wwTbWRE
Svdl6kZNw/trJDipt5C/K3CHgfdfx6WVNkwFuA1qxAL9kUT/co7EsPBSznVALzYf
WW3SGm/DMbBW3ccA9PpYfdkjdx291hW/LU/VPXCdHhh6E+lxN5V87EbGv/ilBQpW
15omMn4M6cz9GS7GUpu9SnNb7sXsub5vXNkL8WJjmdte9sbhA/0847x884I96pZg
JT2naXEyipq4qbelTivaiAkt56hiatGP+6VqeU2erfdpMTCyssOgMi+vFbT2VY5p
Rm6KofIRpHtbmg9jdYrUu0/LfcLMD95qb4zxT8EdEw==
-----END CERTIFICATE-----
)EOF";

const char client_cert[] PROGMEM = R"EEEEEOOOOOFFF1(
-----BEGIN CERTIFICATE-----
PUT YOUR OWN CERTIFICATE HERE
-----END CERTIFICATE-----
)EEEEEOOOOOFFF1";

const char client_key[] PROGMEM = R"EEEEEOOOOOFFF2(
-----BEGIN RSA PRIVATE KEY-----
PUT YOUR OWN PRIVATE KEY HERE
-----END RSA PRIVATE KEY-----
)EEEEEOOOOOFFF2";

BearSSL::X509List ca_crt(ca_cert);
BearSSL::X509List client_crt;
BearSSL::PrivateKey priv_key;

char mqtt_ident[11] = "ERR";

void sslcert_init()
{
    uint8_t tmp[2000];
    File f;
    // check for client name
    if (!SPIFFS.exists("/client.name"))
    {
        println_dbg("SSL:client_name doesnt exist,saving...");
        f = SPIFFS.open("/client.name", "w");
        memcpy_P(tmp, mqtt_ident_p, sizeof(mqtt_ident_p));
        f.write(tmp, sizeof(mqtt_ident_p));
        f.close();
    }
    // check client.crt
    if (!SPIFFS.exists("/client.crt"))
    {
        println_dbg("SSL:client_cert doesnt exist,saving...");
        f = SPIFFS.open("/client.crt", "w");
        memcpy_P(tmp, client_cert, sizeof(client_cert));
        f.write(tmp, sizeof(client_cert));
        f.close();
    }
    // check client.key
    if (!SPIFFS.exists("/client.key"))
    {
        println_dbg("SSL:client_key doesnt exist,saving...");
        f = SPIFFS.open("/client.key", "w");
        memcpy_P(tmp, client_key, sizeof(client_key));
        f.write(tmp, sizeof(client_key));
        f.close();
    }
    // load client.name
    f = SPIFFS.open("/client.name", "r"); // it must exist so we dont even have to check
    f.readBytes((char *)mqtt_ident, f.size());
    print_dbg("SSL:client_name done:");
    println_dbg(mqtt_ident);
    // load client.crt
    f = SPIFFS.open("/client.crt", "r"); // it must exist so we dont even have to check
    f.readBytes((char *)tmp, f.size());
    client_crt.append((char *)tmp);
    println_dbg("SSL:client_cert done");
    // load client.crt
    f = SPIFFS.open("/client.key", "r"); // it must exist so we dont even have to check
    f.readBytes((char *)tmp, f.size());
    priv_key.parse((char *)tmp);
    println_dbg("SSL:client_key done");
}