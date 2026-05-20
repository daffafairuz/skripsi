@extends('layouts.app')

@section('content')

<div>

    <!-- HEADER -->

    <div class="flex justify-between items-center mb-6">

        <div>
            <h1 class="text-2xl font-bold">
                {{ $device->name }}
            </h1>

            <p class="text-gray-500">
                {{ $device->mac_address }}
            </p>
        </div>

        @include('layouts.user-card', ['subtitle' => 'Device Telemetry'])

    </div>


    <!-- SENSOR CARD -->

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        @foreach($device->sensors as $sensor)

        @php
        $latest=$sensor->dataSensors->first();
        @endphp

        <div

        data-sensor="{{ $sensor->id }}"

        class="sensor-card
        bg-white
        rounded-xl
        shadow
        p-5
        cursor-pointer
        hover:ring-2
        hover:ring-green-500">

            <p class="text-sm text-gray-400">

                {{ $sensor->name }}

            </p>

            <p class="text-3xl font-bold mt-3">

                {{ $latest->value ?? '-' }}

            </p>

            <p class="text-sm text-gray-500">

                {{ $sensor->unit }}

            </p>

        </div>

        @endforeach

    </div>


    <!-- CHART -->

    <div class="bg-white rounded-xl shadow p-6">

        <h3
        id="chartTitle"
        class="font-bold mb-5">

            Riwayat Sensor

        </h3>

        <div style="height:400px">

            <canvas id="sensorChart"></canvas>

        </div>

    </div>

</div>

@endsection


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

let chart=null;

document.addEventListener(
'DOMContentLoaded',
()=>{

const cards=
document.querySelectorAll(
'.sensor-card'
);


function loadChart(id)
{

fetch(
`/sensor/${id}/chart`
)

.then(
res=>res.json()
)

.then(data=>{

console.log(data);


if(
data.values.length===0
)
{

data.labels=[

'08:00',
'09:00',
'10:00',
'11:00',
'12:00'

];

data.values=[

20,
25,
23,
28,
26

];

}


document
.getElementById(
'chartTitle'
)
.innerText=

'Riwayat '+data.sensor;


const ctx=
document
.getElementById(
'sensorChart'
);


if(chart)
{

chart.destroy();

}


chart=
new Chart(
ctx,
{

type:'line',

data:{

labels:
data.labels,

datasets:[{

label:

data.sensor+
' ('+
data.unit+
')',

data:
data.values,

borderWidth:2,

tension:0.4,

fill:true

}]

},

options:{

responsive:true,

maintainAspectRatio:false

}

}

);

});

}


cards.forEach(card=>{

card.addEventListener(
'click',
()=>{

loadChart(
card.dataset.sensor
);

});

});


if(cards.length)
{

loadChart(
cards[0]
.dataset
.sensor
);

}

});

</script>