<div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100 flex-shrink-0">
    <div class="text-right hidden sm:block">
        <p class="text-xs font-bold text-gray-800 leading-none">
            {{ auth()->user()->name ?? 'User' }}
        </p>
        <p class="text-[10px] text-blue-500 font-medium italic">
            {{ $subtitle ?? (auth()->user()->role === 'admin' ? 'Administrator' : 'User') }}
        </p>
    </div>
    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-100 flex-shrink-0">
        {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
    </div>
</div>
