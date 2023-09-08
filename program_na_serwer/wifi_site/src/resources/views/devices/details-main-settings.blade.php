<div class="box">
    <div class="box-header">
        <h3 class="box-title">Device</h3>
    </div>
    <div class="box-body no-padding">
        <form class="form-horizontal" method="post" id="device-update">
            {{ csrf_field() }}
            <input type="hidden" name="static-data" value="true">
            <input type="hidden" name="device-id" id="device-id" value="{{ $deviceId }}">
            <div class="box-body">
                <div class="form-group">
                    <label for="device-name" class="col-sm-2 control-label">Name</label>
                    <div class="col-sm-3">
                        <input @if (!$isTeamOwner) disabled @endif class="form-control"
                               id="device-name" name="device-name" placeholder="Device name" type="text"
                               value="{{ $device->name }}">
                    </div>
                </div>
                @if ($isTeamOwner)
                    <div class="form-group">
                        <label for="device-location" class="col-sm-2 control-label">Location</label>
                        <div class="col-sm-3">
                            <select name="device-location" id="device-location" style="width: 100%">
                                <option></option>
                                @foreach($locations as $location)
                                    <option @if (in_array($location->id, $deviceLocations)) selected="selected"
                                            @endif value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                <div class="form-group">
                    <label for="created-at" class="col-sm-2 control-label" for="created-at">Created at</label>
                    <div class="col-sm-3">
                        <input id="created-at" disabled class="form-control col-sm-3" type="text" value="{{ (new DateTime($device->created_at))->format('Y-m-d H:i:s') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="last-seen" class="col-sm-2 control-label" for="last-seen">Last seen</label>
                    <div class="col-sm-3">
                        <input id="last-seen" disabled class="form-control" type="text" value="{{ (new DateTime($device->last_seen))->format('Y-m-d H:i:s') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="network-name" class="col-sm-2 control-label" for="last-seen">Network name</label>
                    <div class="col-sm-3">
                        <input id="network-name" disabled class="form-control" type="text" value="{{ (isset($modem->net_name) ? $modem->net_name : '') }}">
                    </div>
                </div>
                <div class="form-group">
                    <label for="rssi" class="col-sm-2 control-label" for="last-seen">RSSI</label>
                    <div class="col-sm-3">
                        <input id="rssi" disabled class="form-control" type="text" value="{{ (isset($modem->rssi) ? $modem->rssi : '') }}">
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="overlay hide">
        <span class="fa fa-refresh fa-spin"></span>
    </div>
</div>
