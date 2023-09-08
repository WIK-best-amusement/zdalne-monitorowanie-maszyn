![Unia](./unia.png)

Projekt najlepiej jest otworzyć w darmowym środowisku programistycznym 'Visual Studio Code'', z zainstalowaną wtyczką 'Platformio'

Pobrane zostaną wtedy automatycznie kompilator oraz inne komponent wymagane do skompilowania projektu na moduły WiFi które oparte są o mikrokontroler ESP8266

---

Aby skonfigurować oprogramowanie do pracy z własnym serwerem MQTT do którego będa przesyłane dane z urządzeń, należy zedytować plik 'src/config.h' i ustawić odpowiednie wartości zmiennych:
- WIFI_DEFAULT_SSID - domyślna nazwa sieci WiFi do której ma się łączyć urządzenie
- WIFI_DEFAULT_PASSWORD - domyślne hasło do sieci WiFi do której ma się łączyć urządzenie
- MAIN_MQTT_SERVER_ADDRESS - adres serwera MQTT do którego ma się łączyć urządzenie i wysyłać dane
- MAIN_MQTT_SERVER_PORT - port serwera MQTT do którego ma się łączyć urządzenie i wysyłać dane

---

Następnym krokiem jest skonfigurowanie certyfikatów SSL dla urządzenia oraz serwera do którego będzie się ono łączyć.
W tym celu należy otworzyć plik 'src/ssl_certs.cpp' i wkleić klucz prywatny, certyfikat, oraz nazwe urządzenia do odpowiednio zmiennych:
- mqtt_ident_p
- client_key
- client_cert
Dodatkowo w przypadku łączenia się do własnego serwera MQTT którego klucz jest wygenerowany korzystając z własnego certyfikatu CA, należy także wkleić certyfikat do zmiennej 'ca_cert'

---

Następnie należy skompilować projekt i wgrać go na urządzenie. W tym celu należy podłączyć programator układów esp8266 do komputer i do modułu WiFi, następnie w programie 'Visual Studio Code' wybrać opcję 'Platformio: Upload' i poczekać aż proces się zakończy.

Po poprawnym wgraniu oprogramowania na moduł WiFi, urządzenie powinno się połączyć z siecią WiFi, a następnie z serwerem MQTT i zacząć przesyłać dane.

![Unia](./unia.png)