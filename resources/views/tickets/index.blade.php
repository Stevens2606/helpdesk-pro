<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">Tiket</h2>
            <a href="{{ route('tickets.create') }}"
               class="px-4 py-2 bg-gray-800 text-white rounded-md text-sm">Buat Tiket</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif

        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 text-left">
                    <tr>
                        <th class="p-3">Kode</th>
                        <th class="p-3">Subjek</th>
                        <th class="p-3">Kategori</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Prioritas</th>
                        <th class="p-3">Pelanggan</th>
                        <th class="p-3">Ditugaskan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr class="border-t">
                            <td class="p-3">
                                <a class="text-blue-600" href="{{ route('tickets.show', $ticket) }}">{{ $ticket->code }}</a>
                            </td>
                            <td class="p-3">{{ $ticket->subject }}</td>
                            <td class="p-3">{{ $ticket->category->name }}</td>
                            <td class="p-3">{{ $ticket->status->value }}</td>
                            <td class="p-3">{{ $ticket->priority }}</td>
                            <td class="p-3">{{ $ticket->customer->name }}</td>
                            <td class="p-3">{{ $ticket->assignee?->name ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="p-6 text-center text-gray-500">Belum ada tiket.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $tickets->links() }}</div>
    </div>
</x-app-layout>