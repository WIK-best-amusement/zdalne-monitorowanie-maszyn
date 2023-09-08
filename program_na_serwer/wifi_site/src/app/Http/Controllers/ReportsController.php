<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Reports;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class ReportsController extends Controller
{
    private $periods = ['day', 'week', 'month'];

    /**
     * @return \Illuminate\Http\Response
     */
    public function counters($locationId, Request $request)
    {
        $locationId = (int)$locationId;
        $period = $this->periods[0];

        $from = date('Y-m-d', strtotime("-1 month"));
        $to = date('Y-m-d');

        if ($this->checkDate($request->get('start')) && $this->checkDate($request->get('end'))) {
            $from = $request->get('start');
            $to = $request->get('end');
        }

        if (in_array($request->get('period'), $this->periods)) {
            $period = $request->get('period');
        }

        $location = Location::find($locationId);
        $data = Reports::counters($locationId, $from, $to);
        $devices = Reports::devicesForCounters($locationId);
        $counterReport = Reports::getCounterReport($data, $period, $from, $to);

        return view('reports.counters', ['pageTitle' => 'Report for: '.$location->name, 'reportData' => $counterReport, 'devices' => $devices, 'locationId' => $locationId, 'period' => $period, 'from' => $from, 'to' => $to]);
    }

    private function checkDate($date)
    {
        $tempDate = explode('-', $date);

        if (!isset($tempDate[0]) || !isset($tempDate[1]) || !isset($tempDate[2])) {
            return false;
        }

        $tempDate[0] = intval($tempDate[0]);
        $tempDate[1] = intval($tempDate[1]);
        $tempDate[2] = intval($tempDate[2]);

        return checkdate($tempDate[1], $tempDate[2], $tempDate[0]);
    }

}
