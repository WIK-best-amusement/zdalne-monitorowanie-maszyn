<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Models\Device;
use App\Models\DeviceProblem;
use App\Models\DeviceSetting;
use App\Models\DeviceSettingsHistory;
use App\Models\Location;
use App\Models\LocationsDevice;
use App\Models\Modem;
use App\Models\Option;
use App\Models\OptionsGroupsRole;
use Barryvdh\Debugbar\Middleware\Debugbar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use phpDocumentor\Reflection\Types\Integer;

/**
 * Class DevicesController
 * @package App\Http\Controllers
 */
class DevicesController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */

    public function index()
    {
        if (\Session::get('teamOwner')) {
            $devices = Device::getDevicesForTeamOwner();
        } else {
            $devices = Device::getDevicesForTeamMember(Auth::user()->id);
        }

        return view('devices.list', ['pageTitle' => 'Devices list', 'devices' => $devices, 'locationId' => null, 'location' => null]);
    }

    /**
     * @param int $groupId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function devicesByLocation($locationId)
    {
        $locationId = (int)$locationId;
        $devices =  Device::getDeviceByLocation($locationId);
        $location = Location::find($locationId);
        return view('devices.list', ['pageTitle' => 'Devices list', 'devices' => $devices, 'locationId' => $locationId, 'location' => $location]);
    }

    /**
     * @param int $deviceId
     * @return view
     */
    public function details($deviceId)
    {
        $device = Device::find($deviceId);
        $modem = Modem::find($device->modem_id);

        $locations = Location::getLocationsByTeam(Session::get('teamId'));
        $deviceLocations = LocationsDevice::getLocationsForDevice($deviceId);

        $userType = null;
        if (Session::get('teamOwner') === false) {
            $userType = Location::getUserTypeInLocation($deviceId, Auth::user()->id);
        }

        $details = DeviceSetting::getDetails($deviceId, $userType);

        \Barryvdh\Debugbar\Facade::addMessage('User type ' . $userType);

        $detailsGrouped = [];
        foreach ($details as $detail) {
            $detailsGrouped[$detail->location_name][] = $detail;
        }

        return view('devices.details', [
            'modem' => $modem,
            'userType' => $userType,
            'pageTitle' => 'Device details',
            'detailsGrouped' => $detailsGrouped,
            'deviceId' => $deviceId,
            'locations' => $locations,
            'device' => $device,
            'deviceLocations' => $deviceLocations,
            'isTeamOwner' => Session::get('teamOwner'),
            'selectOptions' => OptionsGroupsRole::getOnlySelectOptions()
        ]);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function update(Request $request)
    {
        $deviceId = (int)$request->get('deviceId');
        $requestData = $request->get('data');

        Device::updateDeviceBasicSettings($requestData, $deviceId);
        $updatedSettings = Device::updateDeviceAdditionalSettings($requestData, $deviceId);

        return response()->json(['response' => 'Device was updated', 'data' => ['updatedSettings' => $updatedSettings]]);
    }

    /**
     * @param $deviceId
     * @param $settingId
     * @return mixed
     */
    public function settingsHistory($deviceId, $settingId)
    {
        //Todo Tu chyba powinno być sprawdzenie czy użytkownik może zobaczyć dany settings - wg roli
        $deviceId = (int)$deviceId;
        $settingId = (int)$settingId;

        $setting = DeviceSetting::where('id', $settingId)->where('device_id', $deviceId)->first();
        $option = Option::find($setting->option_id);

        return view('devices.history', [
            'history' => DeviceSettingsHistory::getHistory($settingId),
            'option' => $option
        ]);
    }

    /**
     * @param $id
     * @param $deviceId
     */
    public function errorsMarkAsRead($id, $deviceId)
    {
        DeviceProblem::where('id', $id)
            ->where('device_id', $deviceId)
            ->update(['displayed' => 0]);
    }
}
