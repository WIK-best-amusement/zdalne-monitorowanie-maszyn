require 'influxdb'

class InfluxLogger
  def initialize
    begin

    rescue
      $logger.info("influxdb connection error")
    end



    #connect to influxdb
    @influxdb = InfluxDB::Client.new 'wifi',#ENV['APP_INFLUX_DATABASE'],
                                     #username: ENV['APP_INFLUX_USERNAME'],
                                     #password: ENV['APP_INFLUX_PASSWORD'],
                                     host: 'influxdb'#ENV['APP_INFLUX_HOST']

    #check if 'wifi' database already exists, if it doesn't create it
    @influxdb.create_database('wifi') unless @influxdb.list_databases.map {|x| x['name']}.include?('wifi')
  end

  def save_value(dev_id,devopt,val)
    begin
      data = {
          values: { devopt.to_sym => val},
          tags:   { dev_id: dev_id}   #, node_2_id: node_2_id, led_network_id: lednet_id }
      }
      @influxdb.write_point('device_data', data)
    rescue
      $logger.debug("influxdb save value error(device)")
    end
  end
  def save_mod_value(modem_id,modem_opt,val)
    begin
      data = {
          values: { modem_opt.to_sym => val},
          tags:   { modem_id: modem_id}
      }
      @influxdb.write_point('modem_data', data)
    rescue
      $logger.debug("influxdb save value error(modem)")
    end
  end
end