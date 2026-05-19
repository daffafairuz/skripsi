<!-- DELETE MODAL -->

<div
    x-show="openDelete"
    x-transition
    class="fixed inset-0 bg-black/40 z-50 flex items-center justify-center p-4"
    style="display:none">

    <div
        @click.away="openDelete=false"
        class="bg-white rounded-2xl shadow-xl w-full max-w-md">

        <!-- HEADER -->
        <div class="p-6 text-center">

            <!-- Warning Icon -->
            <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">

                <svg
                    class="w-8 h-8 text-red-600"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>

                </svg>

            </div>

            <h2 class="text-xl font-bold mb-2">

                Hapus Device?

            </h2>

            <p class="text-gray-500">

                Device

                <span
                    class="font-semibold"
                    x-text="selectedDevice.name">
                </span>

                akan dihapus permanen.

            </p>

        </div>


        <!-- WARNING BOX -->

        <div class="mx-6 mb-6 p-4 bg-red-50 rounded-xl">

            <div class="flex gap-3">

                <svg
                    class="w-5 h-5 text-red-600 flex-shrink-0"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24">

                    <path
                        stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01"/>

                </svg>

                <div>

                    <p class="font-medium text-red-700">

                        Tindakan ini akan:

                    </p>

                    <ul class="text-sm text-red-600 mt-2 space-y-1">

                        <li>
                            • Menghapus device ESP
                        </li>

                        <li>
                            • Menghapus seluruh sensor
                        </li>

                        <li>
                            • Menghapus seluruh actuator
                        </li>

                        <li>
                            • Menghapus riwayat terkait
                        </li>

                    </ul>

                </div>

            </div>

        </div>


        <!-- FOOTER -->

        <form
            :action="'/devices/'+selectedDevice.id"
            method="POST"
            class="p-6 border-t">

            @csrf
            @method('DELETE')

            <div class="flex justify-end gap-3">

                <button
                    type="button"
                    @click="openDelete=false"
                    class="px-5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200">

                    Batal

                </button>

                <button
                    class="px-5 py-2 rounded-xl bg-red-500 hover:bg-red-600 text-white">

                    Hapus

                </button>

            </div>

        </form>

    </div>

</div>