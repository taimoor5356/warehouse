<?php

use App\Models\Setting;
function forecasting($available,$forecastDays,$lastTenDaysLabelDeduction, $html)
{
    $forecastVals = 0;
    $setting = Setting::where('id', '1')->first();
    $forecastDays = 0;
    $threshold = 0;
    if ($forecastDays == NULL || $forecastDays == '') {
        $forecastDays = $setting->forecast_days;
    }
    if ($threshold == NULL || $threshold == '') {
        $threshold = $setting->threshold_val;
    }
    $perDayDeduction = $lastTenDaysLabelDeduction / $forecastDays;
    if (ceil($perDayDeduction) == 0) {
        $perDayDeduction = 0;
    }
    if ($perDayDeduction > 0) {
        $forecastVals = $available / $perDayDeduction; // next forecast Days
        $forecastVals = ceil($forecastVals);
    }
    $start_date = new DateTime(date("Y-m-d"));
    $end_date = new DateTime(date("Y-m-d", strtotime("+$forecastVals days")));
    $dd = date_diff($start_date, $end_date);
    if (isset($threshold)) {
        $threshold = $threshold;
    } else {
        $threshold = 0;
    }
    if ($threshold == 0) {
        $html .= '<span class="badge rounded-pill badge-light-danger me-1">Set Threshold</span>';
    } else {
        if ($forecastVals > $forecastDays) // if next product expiry days > set forecast days
        {
            if ($dd->y < 1) {
                $y = '';
            } else {
                $y = $dd->y . 'y ';
            }
            if ($dd->m < 1) {
                $m = '';
            } else {
                $m = $dd->m . 'm ';
            }
            $html .= '<span class="badge rounded-pill badge-light-success me-1" style="background-color: lightgreen; color: darkgreen">' . $y . $m . $dd->d . 'd' . '</span>';
        } else if ($forecastVals < $forecastDays) // if next product expiry days > set forecast days
        {
            if ($dd->y < 1) {
                $y = '';
            } else {
                $y = $dd->y . 'y ';
            }
            if ($dd->m < 1) {
                $m = '';
            } else {
                $m = $dd->m . 'm ';
            }
            $html .= '<span class="badge rounded-pill badge-light-danger me-1" style="background-color: pink; color: red">' . $y . $m . $dd->d . 'd' . '</span>';
        } else {
            if ($dd->y < 1) {
                $y = '';
            } else {
                $y = $dd->y . 'y ';
            }
            if ($dd->m < 1) {
                $m = '';
            } else {
                $m = $dd->m . 'm ';
            }
            $html .= '<span class="badge rounded-pill badge-light-danger me-1" style="background-color: pink; color: red">' . $y . $m . $dd->d . 'd' . '</span>';
        }
    }
    return $html;
 }