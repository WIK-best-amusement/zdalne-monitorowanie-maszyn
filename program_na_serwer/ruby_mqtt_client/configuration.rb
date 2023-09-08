module Configuration
  #database config
  DATABASE_HOST='mysql'
  DATABASE_USER=ENV['DB_USERNAME']
  DATABASE_PASS=ENV['DB_PASSWORD']
  DATABASE_DB_NAME=ENV['DB_DATABASE']


  #MQTT config
  MQTT_SERVER_ADDRESS = 'online.wik.pl'
  MQTT_ROOT_TOPIC     = ''



end
