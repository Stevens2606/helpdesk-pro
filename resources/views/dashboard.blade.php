@php
    $statusStyle = fn ($status) => match ($status->value) {
        'open'        => 'bg-blue-100 text-blue-800',
        'in_progress' => 'bg-yellow-100 text-yellow-800',
        'resolved'    => 'bg-green-100 text-green-800',
        'closed'      => 'bg-gray-100 text-gray-700',
        default       => 'bg-gray-100 text-gray-700',
    };
    $priorityStyle = fn ($p) => match (strtolower((string) $p)) {
        'urgent', 'high' => 'bg-red-100 text-red-800',
        'medium'         => 'bg-orange-100 text-orange-800',
        'low'            => 'bg-slate-100 text-slate-700',
        default          => 'bg-gray-100 text-gray-700',
    };
    $cards = [
        ['label' => 'Total tiket',   'value' => $stats['total'],      'color' => 'text-gray-900'],
        ['label' => 'Terbuka',       'value' => $stats['open'],       'color' => 'text-blue-600'],
        ['label' => 'Diproses',      'value' => $stats['inProgress'], 'color' => 'text-yellow-600'],
        ['label' => 'Selesai',       'value' => $stats['resolved'],   'color' => 'text-green-600'],
        ['label' => 'Lewat SLA',     'value' => $stats['overdue'],    'color' => 'text-red-600'],
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Halo, {{ auth()->user()->name }}
                <span class="ml-2 text-sm font-normal text-gray-500">
                    {{ $isCustomer ? 'Tiket Anda' : 'Ringkasan semua tiket' }}
                </span>
            </h2>
            <a href="{{ route('tickets.create') }}"
               class="inline-flex items-center px-4 py-2 bg-gray-800 text-white text-xs font-semibold uppercase tracking-widest rounded-md hover:bg-gray-700">
                + Buat Tiket
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                @foreach ($cards as $card)
                    <div class="bg-white shadow-sm sm:rounded-lg p-5">
                        <p class="text-sm text-gray-500">{{ $card['label'] }}</p>
                        <p class="mt-2 text-3xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b">
                    <h3 class="font-semibold text-gray-800">Tiket terbaru</h3>
                    <a href="{{ route('tickets.index') }}" class="text-sm text-indigo-600 hover:underline">Lihat semua</a>
                </div>

                @if ($recentTickets->isEmpty())
                    <p class="px-6 py-10 text-center text-gray-500">Belum ada tiket.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-6 py-3">Kode</th>
                                    <th class="px-6 py-3">Subjek</th>
                                    @unless ($isCustomer)
                                        <th class="px-6 py-3">Pelanggan</th>
                                    @endunless
                                    <th class="px-6 py-3">Status</th>
                                    <th class="px-6 py-3">Prioritas</th>
                                    <th class="px-6 py-3">Batas SLA</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($recentTickets as $ticket)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-3 font-mono text-xs text-gray-600">{{ $ticket->code }}</td>
                                        <td class="px-6 py-3">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="text-indigo-600 hover:underline">
                                                {{ \Illuminate\Support\Str::limit($ticket->subject, 50) }}
                                            </a>
                                        </td>
                                        @unless ($isCustomer)
                                            <td class="px-6 py-3">{{ $ticket->customer?->name }}</td>
                                        @endunless
                                        <td class="px-6 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $statusStyle($ticket->status) }}">
                                                {{ str($ticket->status->value)->replace('_', ' ')->title() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3">
                                            <span class="px-2 py-1 rounded-full text-xs font-medium {{ $priorityStyle($ticket->priority) }}">
                                                {{ ucfirst($ticket->priority) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-3 {{ $ticket->due_at?->isPast() && ! in_array($ticket->status->value, ['resolved','closed']) ? 'text-red-600 font-medium' : 'text-gray-600' }}">
                                            {{ $ticket->due_at?->format('d M Y H:i') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>