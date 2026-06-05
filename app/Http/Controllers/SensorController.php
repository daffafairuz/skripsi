<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Sensor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SensorController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $sensors = Sensor::with('device')->latest()->get();
        $devices = Device::all();

        return view('sensors.index', compact('sensors', 'devices'));
    }

    /*
    |--------------------------------------------------------------------------
    | DEVICE - Show sensors for a specific device
    |--------------------------------------------------------------------------
    */

    public function device(Device $device)
    {
        $user = auth()->user();
        $device->load([
            'sensors.dataSensors' => function ($query) use ($device, $user) {
                $this->applyConnectionPeriodFilter($query, request('site_id'), $device->id, $user, request());
                $query->latest()->take(20);
            }
        ]);

        return view(
            'sensors.device',
            compact('device')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CHART - Return chart data for a sensor
    |--------------------------------------------------------------------------
    */

    public function chart(Request $request, Sensor $sensor)
    {
        $query = $sensor->dataSensors();
        $user = auth()->user();

        $this->applyConnectionPeriodFilter($query, $request->input('site_id'), $sensor->device_id, $user, $request);

        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('start_date') || $request->filled('end_date')) {
            $data = $query->orderBy('created_at', 'asc')->get();
        } else {
            $data = $query->latest()->take(100)->get()->reverse();
        }

        $firstData = $sensor->dataSensors()->orderBy('created_at', 'asc')->first();
        $minDate = $firstData ? $firstData->created_at->format('Y-m-d') : now()->format('Y-m-d');
        $maxDate = now()->format('Y-m-d');

        return response()->json([
            'labels' => $data->map(fn($d) => $d->created_at->format('d/m H:i'))->values()->all(),
            'values' => $data->map(fn($d) => (float) $d->value)->values()->all(),
            'sensor' => $sensor->name,
            'unit' => $sensor->unit,
            'min_date' => $minDate,
            'max_date' => $maxDate,
        ]);
    }

    public function updateThreshold(Request $request, Sensor $sensor)
    {
        $request->validate([
            'min_threshold' => 'nullable|numeric',
            'max_threshold' => 'nullable|numeric',
        ]);

        $sensor->update([
            'min_threshold' => $request->min_threshold,
            'max_threshold' => $request->max_threshold,
        ]);

        return back()->with('success', 'Threshold untuk sensor ' . $sensor->name . ' berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'type' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'min_threshold' => 'nullable|numeric',
            'max_threshold' => 'nullable|numeric',
        ], [
            'name.required' => 'Nama sensor wajib diisi',
            'device_id.required' => 'Device wajib dipilih',
            'type.required' => 'Tipe sensor wajib diisi',
            'unit.required' => 'Satuan wajib diisi',
        ]);

        Sensor::create([
            'name' => $request->name,
            'device_id' => $request->device_id,
            'type' => $request->type,
            'unit' => $request->unit,
            'min_threshold' => $request->min_threshold,
            'max_threshold' => $request->max_threshold,
        ]);

        return redirect('/sensors')
            ->with('success', 'Sensor berhasil ditambahkan');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, Sensor $sensor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device_id' => 'required|exists:devices,id',
            'type' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'min_threshold' => 'nullable|numeric',
            'max_threshold' => 'nullable|numeric',
        ]);

        $sensor->update([
            'name' => $request->name,
            'device_id' => $request->device_id,
            'type' => $request->type,
            'unit' => $request->unit,
            'min_threshold' => $request->min_threshold,
            'max_threshold' => $request->max_threshold,
        ]);

        return redirect('/sensors')
            ->with('success', 'Sensor berhasil diperbarui');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(Sensor $sensor)
    {
        $sensor->delete();

        return redirect('/sensors')
            ->with('success', 'Sensor berhasil dihapus');
    }

    private function applyConnectionPeriodFilter($query, $selectedSiteId, $selectedDeviceId, $user, $request, $dateColumn = 'created_at')
    {
        // 1. Admin bypass check
        if ($user->role === 'admin') {
            $scope = $request->input('scope', 'all');
            if ($scope === 'all') {
                return; // Admin wants to see everything, no filter
            }
        }

        // 2. Fetch periods
        $periodsQuery = DB::table('site_devices');

        if ($user->role === 'admin') {
            if ($selectedSiteId) {
                $periodsQuery->where('site_id', $selectedSiteId);
            } else {
                return;
            }
        } else {
            $userSiteIds = $user->sites->pluck('id')->toArray();
            if (empty($userSiteIds)) {
                $query->whereRaw('1 = 0');
                return;
            }
            if ($selectedSiteId && in_array($selectedSiteId, $userSiteIds)) {
                $periodsQuery->where('site_id', $selectedSiteId);
            } else {
                $periodsQuery->whereIn('site_id', $userSiteIds);
            }
        }

        if ($selectedDeviceId) {
            $periodsQuery->where('device_id', $selectedDeviceId);
        }

        $periods = $periodsQuery->get(['device_id', 'started_at', 'ended_at']);

        if ($periods->isEmpty()) {
            $query->whereRaw('1 = 0');
            return;
        }

        // Group periods by device_id
        $periodsByDevice = $periods->groupBy('device_id');
        $deviceIds = $periods->pluck('device_id')->unique()->toArray();

        // Get sensors for these devices
        $sensorsByDevice = Sensor::whereIn('device_id', $deviceIds)->get(['id', 'device_id'])->groupBy('device_id');

        $query->where(function ($q) use ($periodsByDevice, $sensorsByDevice, $dateColumn) {
            $isFirstDevice = true;
            foreach ($periodsByDevice as $deviceId => $devicePeriods) {
                $sensorIds = $sensorsByDevice->get($deviceId, collect())->pluck('id')->toArray();
                if (empty($sensorIds)) {
                    continue;
                }
                
                $q->{ $isFirstDevice ? 'where' : 'orWhere' }(function ($subQ) use ($sensorIds, $devicePeriods, $dateColumn) {
                    $subQ->whereIn('sensor_id', $sensorIds);
                    $subQ->where(function ($dateQ) use ($devicePeriods, $dateColumn) {
                        foreach ($devicePeriods as $idx => $period) {
                            $started = $period->started_at;
                            $ended = $period->ended_at;
                            $dateQ->{ $idx === 0 ? 'where' : 'orWhere' }(function ($innerQ) use ($started, $ended, $dateColumn) {
                                $innerQ->where($dateColumn, '>=', $started);
                                if ($ended) {
                                    $innerQ->where($dateColumn, '<=', $ended);
                                }
                            });
                        }
                    });
                });
                $isFirstDevice = false;
            }
        });
    }
}
