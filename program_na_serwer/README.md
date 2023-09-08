Wszystkie komponenty projektu zostały przygotowane aby uruchomić je z pomocą narzędzia 'Docker' i Docker compose.
Dzięki temu uruchomienie własnej testowej instancji systemu jest bardzo proste.
Pierwszym krokiem w celu uruchomienia systemu jest zainstalowanie narzędzia Docker oraz Docker compose.
Następnie należy wgrać lub wygenerować własny certyfikat SSL dla serwera i umieścić go w katalogu `./mosquitto_config/keys`.
Należy także wygenerować klucze dla klienta 'ruby_mqtt_client' i umieścić je w katalogu `./ruby_mqtt_client/keys`.
Następnie należy uruchomić komendę `docker-compose up` w katalogu głównym projektu. 

Przy pierwszym starcie systemu, trzeba ręcznie zaimportować strukturę bazy danych z pliku `./online_wik_pl_struct.sql`. Używając polecenia `docker exec -it wifi_mysql bash` można uzyskać dostęp do kontenera i wykonać polecenie `mysql -u root -p < online_wik_pl_struct.sql` aby zaimportować strukturę bazy danych.

Opis struktury katalogów

/mosquitto_config
  - konfiguracja dla servera mosquitto ( server MQTT )
  - łączą się do niego(do serwera MQTT) urządzenia(moduły WiFi + ruby_mqtt_client który zapisuje informacje z urządzeń w bazie mysql, a także wysyła polecenia zmiany ustawień do urządzeń(polecenia też są wysyłane przez mqtt)

/persistance
  - nie istnieje w repozytorium bo dodany do .gitignore'a
  - tworzy się po włączeniu wszystkiego przez docker-compose'a , trzymana jest tam baza danych itd

/ruby_mqtt_client
  - program w ruby który łączy się do serwera MQTT, subskrybuje do tematów od urządzeń i zapisuje przysyłane przez nie dane do bazy danych mysql
  - sprawdza też tabele device_settings_pending i jak coś tam znajdzie to publish'uje do odpowiednich tematów od urządzeń polecenia zmiany ustawień

/wifi_site
  - strona w laravel'u pozwalająca na zarządzanie urządzeniami

/wifi_site_to_ruby_connector
  - server weboscket'ów do którego łączy się ruby_mqtt_client i i stronka(w sensie w zasadzie to klienci)
  - przesyłane przez niego są informacje o zmianach ustawień na urządzeniach( i po zmianie na poprawną wartość znika ten napis pending przy urządzeniu

/online_wik_pl_struct.sql
  - struktura bazy danych, do wrzucenia na serwer

/docker-compose.yml
  - plik konfiguracyjny dla docker-compose'a


![Unia](./unia.png)