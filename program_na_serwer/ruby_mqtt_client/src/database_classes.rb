require 'rubygems'
require 'active_record'
require 'safe_attributes/base'
require_relative '../configuration'

  ActiveRecord::Base.inheritance_column='inh_type'
  ActiveRecord::Base.establish_connection(
      :adapter => 'mysql2',
      :host => Configuration::DATABASE_HOST,
      :database => Configuration::DATABASE_DB_NAME,
      :username => Configuration::DATABASE_USER,
      :password => Configuration::DATABASE_PASS
  )
  ActiveRecord::Base.default_timezone= :local
  ActiveRecord::Base.time_zone_aware_attributes = false



  # class Device <ActiveRecord::Base
  #   has_many :deviceSettings
  #   has_many :deviceSettingsPending
  #   belongs_to :device_group
  #   #belongs_to :user, through: :device_groups
  #   validates :serial_number, presence: true
  #   validates :challenge_response, presence: true
  #
  # end

  class Modem <ActiveRecord::Base
    has_many :devices
    belongs_to :team
    #belongs_to :user, through: :device_groups
    validates :serial_number, presence: true
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
  # class User <ActiveRecord::Base
  #   has_many :devices, through: :device_groups
  #   has_many :device_groups
  #   validates :email, presence: true
  #   validates :username, presence: true
  #   validates :password_hash, presence: true
  # end

  # class DeviceGroup <ActiveRecord::Base
  #   self.table_name = 'device_groups' #TODO: this might be not needed
  #   belongs_to :user
  #   has_many :devices
  #   has_many :modules
  #   validates :name, presence: true
  #   validates :group_hash, presence: true
  # end

  class Team <ActiveRecord::Base
    include SafeAttributes::Base
    belongs_to :user
    has_many :devices
    has_many :modules
    bad_attribute_names :hash
    validates :name, presence: true
    validates :hash, presence: true
  end

  class DeviceSettingsPending <ActiveRecord::Base #just a test
    self.table_name = 'device_settings_pending'
    belongs_to :device
    belongs_to :option
    validates :device_id, presence: true
    validates :option_id, presence: true
    validates :value, presence: true
  end
