<?php

namespace App;

use App\Models\LocationsDevice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Reports extends Model
{
    public static function counters($locationId, $from, $to)
    {
        $day = "DATE_FORMAT(`device_settings_history`.date, '%Y-%m-%d') AS day";
        $week = "DATE_FORMAT(`device_settings_history`.date, '%Y-%u') AS week";
        $month = "DATE_FORMAT(`device_settings_history`.date, '%Y-%m') AS month";

        $subQuery = DB::table('device_settings')
            ->select(DB::raw('MAX(device_settings_history.value)'))
            ->leftJoin('device_settings_history', function ($join) use ($from) {
                $join->on('device_settings_history.device_setting_id', '=', 'device_settings.id')
                    ->on('device_settings_history.date', '<', DB::raw("'".$from."'"));
            })
            ->where('device_settings.option_id', 1)
            ->where('device_settings.device_id', '=', DB::raw('locations_devices.device_id'));


        return LocationsDevice::select('device_settings_history.value', 'devices.name', 'locations_devices.device_id', 'device_settings_history.date', DB::raw($day), DB::raw($week), DB::raw($month))
            ->selectSub($subQuery, 'aggregated')
            ->leftJoin('device_settings', 'device_settings.device_id', '=', 'locations_devices.device_id')
            ->leftJoin('device_settings_history', 'device_settings_history.device_setting_id', '=', 'device_settings.id')
            ->leftJoin('devices', 'devices.id', '=', 'locations_devices.device_id')
            ->where('location_id', $locationId)
            ->where('device_settings_history.date', '>=', $from)
            ->where('device_settings_history.date', '<', DB::raw('DATE_ADD("'.$to.'", INTERVAL +1 DAY)  '))
            ->where('device_settings.option_id', 1)
            ->groupBy('locations_devices.device_id', 'date', 'day', 'week', 'month', 'value')
            ->orderBy('device_id', 'asc')
            ->orderBy('date', 'asc')
            ->get();
    }

    public static function devicesForCounters($locationId)
    {
        return LocationsDevice::select(DB::raw('distinct(locations_devices.device_id)'), 'devices.name', 'devices.serial_number')
            ->leftJoin('devices', 'devices.id', '=', 'locations_devices.device_id')
            ->where('location_id', $locationId)
            ->orderBy('name')
            ->get();
    }

    /**
     * @param $data
     * @param $period
     * @return array
     */
    public static function getCounterReport($data, $period, $from, $to)
    {
        $counterValueFromPreviousPeriod = [];
        $report = [];

        foreach ($data as $row) {
            $mockedCounterFromPreviousPeriod = false;
            if (!isset($counterValueFromPreviousPeriod[$row->device_id])) {
                if ($row->aggregated == '') {
                    $counterValueFromPreviousPeriod[$row->device_id] = $row->value;
                    $mockedCounterFromPreviousPeriod = true;
                } else {
                    $counterValueFromPreviousPeriod[$row->device_id] = $row->aggregated;
                }
            }

            if ($mockedCounterFromPreviousPeriod || $counterValueFromPreviousPeriod[$row->device_id] > $row->value) {
                $value = $row->value;
            } else {
                $value = $row->value - $counterValueFromPreviousPeriod[$row->device_id];
            }

            $counterValueFromPreviousPeriod[$row->device_id] = $row->value;

            if (!isset($report[$row->{$period}][$row->device_id])) {
                $report[$row->{$period}][$row->device_id] = $value;
            } else {
                $report[$row->{$period}][$row->device_id] += $value;
            }
        }
        return self::generateMissingDays($period, $from, $to, $report);
    }

    public static function generateMissingDays($period, $from, $to, $report)
    {
        $begin = new \DateTime($from);
        $end = new \DateTime($to);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($begin, $interval, $end);

        $arr = [];
        switch ($period) {
            case 'day':
                $format = 'Y-m-d';
                break;
            case 'week':
                $format = 'Y-W';
                break;
            case 'month':
                $format = 'Y-m';
                break;
        }

        foreach ($dateRange as $date) {
            $key = $date->format($format);
            if (array_key_exists($key, $report)) {
                $arr[$key] = $report[$key];
            } else {
                $arr[$key] = [];
            }
        }

        return $arr;
    }
}
