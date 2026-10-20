<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Laporan</h2>
            <a href="{{ route('reports.export') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">Export Excel</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto sm:px-6 lg:px-8 grid gap-6 md:grid-cols-3">
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-500">Total tiket</div>
            <div class="text-3xl font-bold">{{ $total }}</div>
        </div>
        <div class="bg-white shadow rounded p-6">
            <div class="text-sm text-gray-500">SLA terlewati</div>
            <div class="text-3xl font-bold text-red-600">{{ $breached }}</div>
        </div>

        <div class="bg-white shadow rounded p-6 md:col-span-3">
            <h3 class="font-semibold mb-2">Per status</h3>
            @foreach ($byStatus as $status => $count)
                <div class="flex justify-between border-b py-1"><span>{{ $status }}</span><b>{{ $count }}</b></div>
            @endforeach
        </div>

        <div class="bg-white shadow rounded p-6 md:col-span-3">
            <h3 class="font-semibold mb-2">Per kategori</h3>
            @foreach ($byCategory as $cat)
                <div class="flex justify-between border-b py-1"><span>{{ $cat->name }}</span><b>{{ $cat->tickets_count }}</b></div>
            @endforeach
        </div>
    </div>
</x-app-layout>