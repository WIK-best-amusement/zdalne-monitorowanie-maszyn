require 'faye/websocket'
require 'eventmachine'
require 'thread'
require 'json'
class WebsocketClient
  attr_accessor :ws_inst,:ws_thread
  def initialize(server_address='wss://ws.online.wik.pl/sendUpdate')
    @server_address=server_address
    @ws_inst=nil
    @ws_thread=nil
  end
  def run
    $ws_thread=Thread::new {
      begin
        while true
          EM.run {
            ws = Faye::WebSocket::Client.new(@server_address)

            ws.on :open do |event|
              #$logger.info 'ws(wifi_site_to_ruby_connector) connection OK'
              @ws_inst=ws
            end

            ws.on :message do |event|
              #process_data_from_server(JSON.parse(event.data))
            end

            ws.on :close do |event|
              #$logger.info 'ws(wifi_site_to_ruby_connector) got disconnected'
              @ws_inst=nil
              ws = nil
              EM.stop_event_loop
            end
          }

          sleep 2
        end
      rescue Exception
        puts $@
        puts $!
      end
    }
  end
  def process_data_from_server(msg)
    #abcd
  rescue Exception
    puts $@
    puts $!
  end

  def send_data data
    return if @ws_inst.nil?
    @ws_inst.send data
  end

  def send_device_data opt
    hsh={:deviceId=>opt.device_id,:optionId=>opt.option_id,:value=>opt.value,:updated_at=>opt.updated_at}
    #$logger.info "ws(wifi_site_to_ruby_connector) sending:#{hsh.inspect}"
    send_data hsh.to_json
  end
  private


end
