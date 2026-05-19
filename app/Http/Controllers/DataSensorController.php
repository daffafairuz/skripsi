<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DataSensor;
use App\Models\Sensor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DataSensorController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'admin') {
            // Admin can see all sensor data from all users/sites
            $rawSensors = DataSensor::with(['sensor.device.sites'])
                ->orderBy('created_at', 'desc')
                ->take(3000)
                ->get();
        } else {
            // User can only see sensor data from their own sites (ESP32 Master) and connected devices (ESP32 Slaves)
            $siteIds = $user->sites()->pluck('id');

            if ($siteIds->isEmpty()) {
                $rawSensors = collect();
            } else {
                // Get all devices (Slaves) assigned to the user's sites
                $deviceIds = DB::table('site_devices')
                    ->whereIn('site_id', $siteIds)
                    ->pluck('device_id');

                // Get all sensors associated with those devices
                $sensorIds = Sensor::whereIn('device_id', $deviceIds)->pluck('id');

                $rawSensors = DataSensor::with(['sensor.device.sites'])
                    ->whereIn('sensor_id', $sensorIds)
                    ->orderBy('created_at', 'desc')
                    ->take(3000)
                    ->get();
            }
        }

        // Pivot EAV data: Group by device & 5-minute time window
        $tempGroups = [];

        foreach ($rawSensors as $record) {
            $sensor = $record->sensor;
            if (!$sensor) continue;

            $device = $sensor->device;
            if (!$device) continue;

            // Get first associated site, if any (Site acts as the ESP32 Master gateway)
            $site = $device->sites->first();

            // Round created_at timestamp to the nearest 5-minute interval
            $time = Carbon::parse($record->created_at);
            $minute = $time->minute;
            $roundedMinute = floor($minute / 5) * 5;
            $timeKey = $time->copy()->minute($roundedMinute)->second(0)->format('Y-m-d H:i:00');

            // Unique key per device & time interval
            $groupKey = $timeKey . '_' . $device->id;

            if (!isset($tempGroups[$groupKey])) {
                $tempGroups[$groupKey] = [
                    'waktu' => $timeKey,
                    'device_name' => $device->name, // Slave ESP32
                    'site_name' => $site ? $site->name : '-', // Master ESP32
                    'values' => [
                        'temperature' => null,
                        'humidity' => null,
                        'ph' => null,
                        'tds' => null,
                        'water_level' => null,
                        'dissolved_oxygen' => null,
                        'ec' => null,
                        'soil_moisture' => null,
                        'light' => null,
                    ],
                    'raw_created' => $record->created_at, // used for sorting
                ];
            }

            // Normalise the sensor type to associate with our pivot columns
            $type = strtolower($sensor->type);
            if (array_key_exists($type, $tempGroups[$groupKey]['values'])) {
                $tempGroups[$groupKey]['values'][$type] = $record->value;
            }
        }

        // Format as sorted collection
        $pivotedData = collect(array_values($tempGroups))
            ->sortByDesc('raw_created')
            ->values();

        return view('data_monitoring.riwayat_data_sensor', compact('pivotedData'));
    }
}
