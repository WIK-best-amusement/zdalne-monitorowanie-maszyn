require 'test/unit'
require 'mocha/test_unit'
require_relative '../src/device'

class DeviceTest < Test::Unit::TestCase

  # Called before every test method runs. Can be used
  # to set up fixture information.
  def setup
  end

  # Called after every test method runs. Can be used to tear
  # down fixture information.

  def teardown
    # Do nothing
  end

  # test format_setting_value
  def test_format_setting_value
    dev=Device.new('abc')
    #test formatted options
    assert_equal ['d',3],dev.format_setting_value(['d','Wed'])
    assert_equal ['001',3],dev.format_setting_value(['001','3'])
    assert_equal ['001',3],dev.format_setting_value(['001',3])
    assert_equal ['001',3],dev.format_setting_value(['001','0003'])
    assert_equal ['001',0],dev.format_setting_value(['001','0'])
    assert_equal ['001',nil],dev.format_setting_value(['001','OFF'])
    assert_equal ['001',nil],dev.format_setting_value(['001','  OFF   '])
    assert_equal ['d',3],dev.format_setting_value(['d','Wed'])
    assert_equal ['m',6],dev.format_setting_value(['m','VI'])
    assert_equal ['m',6],dev.format_setting_value(['m','6'])
    assert_equal ['m',6],dev.format_setting_value(['m',6])
    #test normal options
    assert_equal ['002',3],dev.format_setting_value(['002','0003'])
    assert_equal ['002',3],dev.format_setting_value(['002','0003   '])
    assert_equal ['002',0],dev.format_setting_value(['002','   '])
    assert_equal ['002',0],dev.format_setting_value(['002',nil])
    assert_equal ['002',3],dev.format_setting_value(['002','0003   '])
  end
  def test_process_request_config_reply
    $logger=stub(:debug => :result1, :method2 => :result2)
    db_inst=stub(:serial_number => '123', :method2 => :result2)
    dev=Device.new(db_inst)
    dev.stubs({process_single_setting:nil})
    # /\ we have to mock it and check arguments passed to process_single_setting
    test_data='<000=PG:L40517;L1:L10915;L2:L10915;TK:040316;WS:;VP:020416;g1:061216;g2:061216;001=OFF;002=20;003=20;004=99;005=2;006=1;007=2;008=5;009=1;010=2>'
    dev.process_request_config_reply(test_data)
  end
end