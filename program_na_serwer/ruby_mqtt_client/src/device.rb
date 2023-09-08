require 'active_record'

class Device <ActiveRecord::Base
  has_many :deviceSettings
  has_many :deviceSettingsPending
  belongs_to :team
  belongs_to :modem
  validates :serial_number, presence: true




  #converts raw values from mqtt server/device to ones that are good for saving to database
  def format_setting_value(arry)
    arry[1]||=0 #set value to 0 if it was empty
    arry[1].strip! if arry[1].class==String
    transform_hash={
        'd'  => {'Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6, 'Sun' => 0},
        '001'=> {'OFF' => nil},
        'm'  => {'I' => 1,'II' => 2,'III' => 3,'IV' => 4,'V' => 5,'VI' => 6,'VII' => 7,'VIII' => 8,'IX' => 9,'X' => 10,'XI' => 11,'XII' => 12}
    }
    arry[1]=transform_hash[arry[0]][arry[1]] if transform_hash.has_key?(arry[0]) && transform_hash[arry[0]].has_key?(arry[1])
    #arry[1]=arry[1].delete(('A'..'z').to_a.join).to_i if arry[1].class==String
    arry
  end

  #updates devices option value, also saves it to device_setting_histories
  def save_option_value(option,value)
    opt=Option.find_by(dev_rep: option)
    if opt.nil?
      puts "option doesn't exist #{opt}"
      return
    end
    dev_setting=deviceSettings.find_or_create_by(option: opt)
    option,value = format_setting_value([option,value])#this prepares value, eg changes Fri(Friday) to 5, most likely must also be implemented for other languages omg?.
    if dev_setting.value!=value || (dev_setting.id.nil? && value.nil?) #if value changed from the last time it was saved (or val is nil but it wasn't saved yet)
      dev_setting.value=value
      dev_setting.save!
      unless (option=='002' and value==0) or ['M','H'].include?(opt.dev_rep) # save history unless it is 002 counter and it's ==0 or it's minutes or hours
        dev_setting.device_setting_histories.create!(value: dev_setting.value.to_i,date: Time.now)#save history
      end
      $ws_client.send_device_data dev_setting #send option update to websocket site connector that sents live updates to end user
    end

  end

end


