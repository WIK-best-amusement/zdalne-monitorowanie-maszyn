#!/usr/bin/env ruby

require 'rubygems'
require 'sinatra'
require 'erb'
require 'hamlit'
require 'active_support/core_ext/time'

require_relative 'database_classes'


class Time
  def to_cest
    in_time_zone('Europe/Warsaw')
  end
end


use Rack::Auth::Basic, "Restricted Area" do |username, password|
  username == 'admin' and password == 'admin'
end

class App < Sinatra::Application
end


enable :sessions
set :public_folder, 'public'



get "/water_guns" do
  @device_list=Device.where(type_id:11).order(last_seen: :desc).includes(:device_settings,:modem)
  @version_id = [322,323,328,329,14,327]
  @uptime_id = [338,339,340,341,342,343]
  @error_id = [331,332,333,334,335,336]
  haml :water_gun
end


get "/devices/:id" do
  @device=Device.find(params['id'].to_i)
  haml :device_edit
end

post "/devices/:id" do
  @device=Device.find(params['id'].to_i)
  @device.name=params[:device_name]
  #TODO: add checking for .save result, after we add validations it migth fail
  @device.save
  #redirect session.delete(:return_to)
  redirect back
end
