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
    @if ($ticket->attachments->isNotEmpty())
    <ul class="mt-4 text-sm list-disc ml-5">
        @foreach ($ticket->attachments as $file)
            <li>
                <a class="text-blue-600" target="_blank"
                   href="{{ asset('storage/' . $file->path) }}">{{ $file->original_name }}</a>
                ({{ number_format($file->size / 1024, 0) }} KB)
            </li>
        @endforeach
    </ul>
@endif<div class="bg-white shadow rounded p-6 space-y-4">
    <h4 class="font-semibold">Komentar</h4>

    @forelse ($ticket->comments as $comment)
        <div class="border rounded p-3 {{ $comment->is_internal ? 'bg-yellow-50 border-yellow-300' : '' }}">
            <div class="text-xs text-gray-500 mb-1">
                {{ $comment->user->name }} · {{ $comment->created_at->diffForHumans() }}
                @if ($comment->is_internal) · <b>Catatan internal</b> @endif
            </div>
            <p class="whitespace-pre-line">{{ $comment->body }}</p>
            @foreach ($comment->attachments as $file)
                <a class="block text-sm text-blue-600" target="_blank"
                   href="{{ asset('storage/' . $file->path) }}">{{ $file->original_name }}</a>
            @endforeach
        </div>
    @empty
        <p class="text-sm text-gray-500">Belum ada komentar.</p>
    @endforelse

    <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}"
          enctype="multipart/form-data" class="space-y-3">
        @csrf
        <textarea name="body" rows="3" class="w-full border-gray-300 rounded-md"
                  placeholder="Tulis komentar...">{{ old('body') }}</textarea>
        @error('body') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

        <input type="file" name="attachments[]" multiple class="text-sm">
        @error('attachments.*') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

        @if (auth()->user()->role !== 'customer')
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_internal" value="1"> Catatan internal (tidak terlihat pelanggan)
            </label>
        @endif

        @if ($ticket->sla_breached_at)
    <span class="ml-1 px-2 py-0.5 text-xs bg-red-100 text-red-700 rounded">SLA lewat</span>
        @endif

        <button class="px-4 py-2 bg-gray-800 text-white rounded-md">Kirim Komentar</button>
    </form>
</div>
@if (auth()->user()->role !== 'customer')
    <div class="bg-white shadow rounded p-6">
        <h4 class="font-semibold mb-3">Riwayat Aktivitas</h4>
        <ul class="text-sm space-y-2">
            @foreach ($ticket->logs->sortByDesc('created_at') as $log)
                <li class="border-b pb-2">
                    <span class="text-gray-500">{{ $log->created_at->format('d M Y H:i') }}</span>
                    · <b>{{ $log->user?->name ?? 'Sistem' }}</b>
                    · {{ str_replace('_', ' ', $log->action) }}
                    @if ($log->old_value || $log->new_value)
                        : {{ $log->old_value ?? '-' }} → {{ $log->new_value ?? '-' }}
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
@endif
</x-app-layout>