<!-- EDIT MODAL -->

<div
    x-show="openEdit"
    x-transition
    class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
    style="display:none">

    <div
        @click.away="openEdit=false"
        class="bg-white rounded-2xl shadow-xl w-full max-w-4xl max-h-[90vh] overflow-y-auto">

        <!-- HEADER -->
        <div class="flex items-center justify-between p-6 border-b">

            <div>

                <h2 class="text-xl font-bold">
                    Edit Device
                </h2>

                <p class="text-sm text-gray-500">

                    Ubah informasi device dan perangkat terhubung

                </p>

            </div>

            <button
                @click="openEdit=false"
                class="text-gray-500 hover:text-black">

                ✕

            </button>

        </div>

        <!-- FORM -->
        <form
            :action="'/devices/'+selectedDevice.id"
            method="POST"
            class="p-6 space-y-8">

            @csrf
            @method('PUT')

            <!-- ========================= -->
            <!-- DEVICE -->
            <!-- ========================= -->

            <div>

                <h3 class="font-semibold mb-4">

                    Informasi Device

                </h3>

                <div class="grid grid-cols-2 gap-4">

                    <div>

                        <label class="text-sm block mb-2">

                            Nama Device

                        </label>

                        <input
                            type="text"
                            name="name"
                            x-model="selectedDevice.name"
                            class="w-full border rounded-xl p-3">

                    </div>

                    <div>

                        <label class="text-sm block mb-2">

                            MAC Address

                        </label>

                        <input
                            type="text"
                            name="mac_address"
                            x-model="selectedDevice.mac"
                            class="w-full border rounded-xl p-3">

                    </div>

                </div>

                <div class="mt-4">

                    <label class="text-sm block mb-2">

                        Deskripsi

                    </label>

                    <textarea
                        name="description"
                        x-model="selectedDevice.description"
                        rows="3"
                        class="w-full border rounded-xl p-3"></textarea>

                </div>

            </div>

            <!-- ========================= -->
            <!-- SENSOR -->
            <!-- ========================= -->

            <div>

                <div class="flex justify-between mb-4">

                    <h3 class="font-semibold">

                        Sensor

                    </h3>

                    <button
                        type="button"
                        @click="selectedDevice.sensors.push({
                        name:'',
                        type:'',
                        unit:''
                        })"
                        class="bg-blue-500 text-white px-3 py-2 rounded-lg">

                        + Tambah Sensor

                    </button>

                </div>

                <template
                    x-for="(sensor,index) in selectedDevice.sensors"
                    :key="index">

                    <div
                        class="border rounded-xl p-4 mb-3">

                        <div class="grid grid-cols-4 gap-4">

                            <input
                                :name="'sensors['+index+'][name]'"
                                x-model="sensor.name"
                                class="border p-3 rounded-xl"
                                placeholder="Nama">

                            <select
                                :name="'sensors['+index+'][type]'"
                                x-model="sensor.type"
                                class="border p-3 rounded-xl">

                                <option value="temperature">
                                    Temperature
                                </option>

                                <option value="ph">
                                    pH
                                </option>

                                <option value="water_level">
                                    Water Level
                                </option>

                                <option value="humidity">
                                    Humidity
                                </option>

                            </select>

                            <input
                                :name="'sensors['+index+'][unit]'"
                                x-model="sensor.unit"
                                class="border p-3 rounded-xl"
                                placeholder="Unit">

                            <button
                                type="button"
                                @click="
                                selectedDevice.sensors.splice(index,1)
                                "
                                class="bg-red-100 text-red-600 rounded-xl">

                                Hapus

                            </button>

                        </div>

                    </div>

                </template>

            </div>


            <!-- ========================= -->
            <!-- ACTUATOR -->
            <!-- ========================= -->

            <div>

                <div class="flex justify-between mb-4">

                    <h3 class="font-semibold">

                        Actuator

                    </h3>

                    <button
                        type="button"
                        @click="
                        selectedDevice.actuators.push({
                        name:'',
                        type:''
                        })
                        "
                        class="bg-yellow-500 text-white px-3 py-2 rounded-lg">

                        + Tambah Actuator

                    </button>

                </div>


                <template
                    x-for="(actuator,index) in selectedDevice.actuators"
                    :key="index">

                    <div
                        class="border rounded-xl p-4 mb-3">

                        <div class="grid grid-cols-3 gap-4">

                            <input
                                :name="'actuators['+index+'][name]'"
                                x-model="actuator.name"
                                class="border p-3 rounded-xl"
                                placeholder="Nama">

                            <select
                                :name="'actuators['+index+'][type]'"
                                x-model="actuator.type"
                                class="border p-3 rounded-xl">

                                <option value="pump">
                                    Pump
                                </option>

                                <option value="feeder">
                                    Feeder
                                </option>

                                <option value="aerator">
                                    Aerator
                                </option>

                            </select>

                            <button
                                type="button"
                                @click="
                                selectedDevice.actuators.splice(index,1)
                                "
                                class="bg-red-100 text-red-600 rounded-xl">

                                Hapus

                            </button>

                        </div>

                    </div>

                </template>

            </div>


            <!-- FOOTER -->

            <div class="flex justify-end gap-3 border-t pt-6">

                <button
                    type="button"
                    @click="openEdit=false"
                    class="px-5 py-2 rounded-xl bg-gray-100">

                    Batal

                </button>

                <button
                    class="px-5 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white">

                    Update Device

                </button>

            </div>

        </form>

    </div>

</div>