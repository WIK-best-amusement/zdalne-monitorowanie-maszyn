require 'rubygems'
require 'active_record'

# configure database access
active_record_config=File.open( 'db/config.yml' )
ActiveRecord::Base.configurations=YAML::load(active_record_config)
active_record_config.close
ActiveRecord::Base.establish_connection(:production)
#ActiveRecord::Base.logger = Logger::new('./logs.log')

class Device <ActiveRecord::Base
  has_many :device_settings
  #has_many :device_settingsPending
  belongs_to :device_group
  belongs_to :modem
  #belongs_to :user, through: :device_groups
  validates :serial_number, presence: true
  validates :challenge_response, presence: true

  def get_opt(opt_id)
    device_settings.find{|x| x.option_id==opt_id}
  end
end

class Modem <ActiveRecord::Base
  has_many :devices
  belongs_to :device_group
  #belongs_to :user, through: :device_groups
  validates :serial_number, presence: true
  validates :device_group_id, presence: true

end

class DeviceSetting <ActiveRecord::Base
  belongs_to :device
  belongs_to :option
  has_many :device_setting_histories, class_name: 'DeviceSettingHistory'
  validates :device_id, presence: true
  validates :option_id, presence: true
end

class DeviceSettingHistory <ActiveRecord::Base
  self.table_name = 'device_settings_history'
  belongs_to :device_setting
  validates :device_setting_id, presence: true
  validates :date, presence: true
end

class Option <ActiveRecord::Base
  validates :dev_rep, presence: true
  validates :name, presence: true
  has_many :device_settings
end
class User <ActiveRecord::Base
  has_many :devices, through: :device_groups
  has_many :device_groups
  validates :email, presence: true
  validates :username, presence: true
  validates :password_hash, presence: true
end

class DeviceGroup <ActiveRecord::Base
  self.table_name = 'device_groups' #TODO: this might be not needed
  belongs_to :user
  has_many :devices
  has_many :modules
  validates :name, presence: true
  validates :group_hash, presence: true
end

class DeviceSettingsPending <ActiveRecord::Base #just a test
  self.table_name = 'device_settings_pending'
  belongs_to :device
  belongs_to :option
  validates :device_id, presence: true
  validates :option_id, presence: true
  validates :value, presence: true
end