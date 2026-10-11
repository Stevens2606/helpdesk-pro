<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">{{ $ticket->code }}</h2>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
        @if (session('status'))
            <div class="p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif

        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold">{{ $ticket->subject }}</h3>
            <p class="text-sm text-gray-500 mb-4">
                {{ $ticket->category->name }} · oleh {{ $ticket->customer->name }}
                · batas SLA {{ $ticket->due_at?->format('d M Y H:i') }}
            </p>
            <p class="whitespace-pre-line">{{ $ticket->description }}</p>
            <div class="mt-4 text-sm">
                Status: <b>{{ $ticket->status->value }}</b> ·
                Prioritas: <b>{{ $ticket->priority }}</b> ·
                Ditugaskan: <b>{{ $ticket->assignee?->name ?? '-' }}</b>
            </div>
        </div>

        @can('update', $ticket)
            <form method="POST" action="{{ route('tickets.update', $ticket) }}"
                  class="bg-white shadow rounded p-6 space-y-4">
                @csrf @method('PUT')
                <h4 class="font-semibold">Kelola Tiket</h4>

                <select name="status" class="w-full border-gray-300 rounded-md">
                    @foreach (['open', 'in_progress', 'resolved', 'closed'] as $s)
                        <option value="{{ $s }}" @selected($ticket->status->value === $s)>{{ $s }}</option>
                    @endforeach
                </select>

                <select name="priority" class="w-full border-gray-300 rounded-md">
                    @foreach (['low', 'medium', 'high', 'urgent'] as $p)
                        <option value="{{ $p }}" @selected($ticket->priority === $p)>{{ $p }}</option>
                    @endforeach
                </select>

                <select name="assigned_to" class="w-full border-gray-300 rounded-md">
                    <option value="">- Belum ditugaskan -</option>
                    @foreach ($staff as $s)
                        <option value="{{ $s->id }}" @selected($ticket->assigned_to === $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>

                <button class="px-4 py-2 bg-gray-800 text-white rounded-md">Simpan</button>
            </form>
        @endcan
    </div>
</x-app-layout>