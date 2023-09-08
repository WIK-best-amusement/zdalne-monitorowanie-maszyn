#!/usr/bin/env ruby

require "thread"
require_relative 'src/device_server'
require_relative 'src/websocket_client'

$logger = Logger.new(STDERR)
$logger.level = Logger::INFO

$logger.info('Program started')





$tr=Thread.new {
    begin
      $devser=DeviceServer::new(Configuration::MQTT_SERVER_ADDRESS)
      $devser.run_inf_loop

    rescue
      $logger.fatal("#{Time::now}: rescue in main thread #{$@} , #{$!}")
    end
  }

$ws_client=WebsocketClient::new
$ws_thread=$ws_client.run


while $tr.alive? && $ws_thread.alive?
  sleep 30
end
