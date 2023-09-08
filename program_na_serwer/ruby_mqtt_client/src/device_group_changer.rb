module DeviceGroupChanger
  @map={67=>95456,70=>95456}
  @rmap={67=>0,70=>0}
  def self.fake_by_modem_serial(modem_serial)
    @map[modem_serial].to_s
  end
  def self.real_by_modem_serial(modem_serial)
    @rmap[modem_serial].to_s
  end
end