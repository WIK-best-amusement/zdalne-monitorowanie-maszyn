require 'paho-mqtt'
require_relative 'influx_logger'
require_relative '../configuration'
require_relative 'device'
require_relative 'database_classes'
require_relative 'device_group_changer'


class DeviceServer
  attr_accessor :device_list,:socket_list,:ssl_context
  def initialize(mqtt_server_addresss)
    init_mqtt mqtt_server_addresss
    @influxlogger=InfluxLogger.new
  end


  def init_mqtt(server_address)
    @mqtt_client = PahoMqtt::Client.new
    @mqtt_client.host = server_address
    @mqtt_client.port = 8883
    @mqtt_client.ssl = true
    @mqtt_client.blocking = true
    @mqtt_client.persistent = true
    @mqtt_client.config_ssl_context('./keys/client.crt', './keys/client.key', './keys/ca.crt')
    #add message received callback
    mqtt_add_callback
    #connect to server
    @mqtt_client.connect
    #subscribe to topics
    @mqtt_client.subscribe [Configuration::MQTT_ROOT_TOPIC+'+/+/+/+',1]
    @mqtt_client.subscribe [Configuration::MQTT_ROOT_TOPIC+'+/+/+/+/singleshot',1]
  end

  def mqtt_add_callback
    @mqtt_client.on_message do |message|
      message.topic.sub! Configuration::MQTT_ROOT_TOPIC,''  #delete mqtt_root part from topic
      process_dev_data(message.topic,message.payload)
    end
  end


  def run_inf_loop
    while true
      begin
        @mqtt_client.mqtt_loop  # this calls all message callbacks etc.
        check_for_pending_changes
        sleep 0.2
      rescue
        $logger.fatal("rescue in run_inf_loop #{$@} , #{$!}")
      end
    end
  end


  private

  def process_dev_data(topic,msg)
    topic_arry=topic.split '/'
    modem_serial=topic_arry[0]
    group_hash=topic_arry[1]
    #this changes group_hash/client number, based on modem_id
    fake_group_hash=DeviceGroupChanger::fake_by_modem_serial modem_serial.to_i
    group_hash=fake_group_hash if fake_group_hash.length>0

    return unless group_hash.match(/^[a-zA-Z0-9]+$/)
    #get or create a new modem
    mod=Modem.find_by(serial_number: modem_serial)
    if(mod.nil?)
      mod=Modem.new
      #get device/modem group
      team=Team.find_by hash:group_hash
      if team.nil?
        $logger.info "modem GROUP_HASH NOT MATCHING ANY GROUPS dev_id:#{dev_id} grp_hash:#{grp_hash}"
        return nil   #wtf are we supposed to do if it doesnt exist? log it somewhere and what then?
      end
      mod.team=team
      mod.serial_number=modem_serial
      mod.save
    end
    if topic_arry[2]=='mod'
      #process modem data
      mod_opt_name=topic_arry[3]
      case mod_opt_name
      when 'fw_ver'
        mod.firmware_version=msg.to_i
      when 'rssi'
        mod.rssi=msg
      when 'net_name'
        mod.net_name=msg
      end
      @influxlogger.save_mod_value(mod.id.to_s,mod_opt_name,msg.to_i)
      mod.save
    elsif topic_arry[2].match(/^[a-zA-Z0-9]+\.[a-zA-Z0-9]+$/)
      #process device data
      device_serial=topic_arry[2]
      dev=find_device(device_serial,group_hash)
      if dev
        if dev.modem && dev.modem!=mod
          #LOG THIS SOMEWHERE THAT MODEM THAT IS CONNECTED TO THE DEVICE GOT CHANGED
          $logger.info "device modem serial switch dev:#{device_serial},grp_hash:#{group_hash},new_modem_serial:#{modem_serial},old_modem_serial:#{dev.modem.serial_number}"
        end

        dev.modem=mod
        mod.team=dev.team
        mod.save
        dev.type_id=device_serial.split('.')[0].to_i
        dev.last_seen=Time::now
        dev.save
        opt_name=topic_arry[3]
        if opt_name!='audit'
          @influxlogger.save_value(dev.id.to_s,opt_name,msg.to_i)
          dev.save_option_value(opt_name,msg) if opt_name && opt_name.length>0 && opt_name.match(/^[a-zA-Z0-9]+$/)
        else
          @mqtt_client.publish("#{modem_serial}/#{group_hash}/mod/audit_led/exec",'1',false,1)
        end
      end
    end
  end

  def check_for_pending_changes
    #send new options to mqtt broker
    DeviceSettingsPending.where(sent_to_mqtt: 0).take(20).each do |pend_opt|
      #prepare topic and message for mqtt publish
      dev=pend_opt.device
      topic="#{dev.modem.serial_number}/#{dev.team[:hash]}/#{dev.serial_number}/#{pend_opt.option.dev_rep}/set"
      #change fake group_hash to real one(if device has fake hash)
      fake_group_hash=DeviceGroupChanger::real_by_modem_serial dev.modem.serial_number.to_i
      if fake_group_hash.length>0
        topic="#{dev.modem.serial_number}/#{fake_group_hash}/#{dev.serial_number}/#{pend_opt.option.dev_rep}/set"
      end

      next if pend_opt.value.length==0 #ignore values with 0 length

      just_val=pend_opt.option.field_length.to_i
      message=pend_opt.value.to_s.rjust(just_val,'0')

      $logger.debug "publishing to mqtt - topic:#{topic} message:#{message}"
      @mqtt_client.publish(topic,message,true,1)
      pend_opt.sent_to_mqtt=1
      pend_opt.save
    end
    #check options that were already sent
    DeviceSettingsPending.where(sent_to_mqtt: 1).take(20).each do |pend_opt|
      dev=pend_opt.device
      dev_setting=dev.deviceSettings.find_by(option: pend_opt.option)
      $logger.debug "pend_opt id:#{pend_opt.id} delete check: dev_setting:#{dev_setting.value} pend_opt:#{pend_opt.value}"
      pend_opt.delete if dev_setting.value.to_i==pend_opt.value.to_i
    end
  end

  def find_device(dev_id,grp_hash)
    $logger.debug "dev_id:#{dev_id},grp_hash:#{grp_hash}"
    team=Team.find_by hash:grp_hash
    if team.nil?
      $logger.info "device GROUP_HASH NOT MATCHING ANY GROUPS dev_id:#{dev_id} grp_hash:#{grp_hash}"
      return nil   #wtf are we supposed to do if it doesnt exist? log it somewhere and what then?
    end
    dev=Device.find_by serial_number:dev_id , team:team
    if dev.nil?
        #create new device here
        dev=Device.new
        dev.serial_number=dev_id
        dev.team=team
        dev.save
        $logger.info "created new dev successfully sn:#{dev_id}"
    end
    dev
  end

end














