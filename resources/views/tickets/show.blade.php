@php
    $isStaff = auth()->user()->role !== 'customer';
    $done = in_array($ticket->status->value, ['resolved', 'closed']);
    $lateSla = $ticket->sla_breached_at || (! $done && $ticket->due_at?->isPast());
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-gray-800">
                <span class="font-mono text-base text-gray-500">{{ $ticket->code }}</span>
            </h2>
            <a href="{{ route('tickets.index') }}" class="text-sm text-indigo-600 hover:underline">&larr; Kembali ke daftar</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto sm:px-6 lg:px-8">
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('status') }}</div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Kolom utama --}}
            <div class="lg:col-span-2 space-y-6">

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-wrap items-center gap-2 mb-3">
                        <x-status-badge :status="$ticket->status" />
                        <x-priority-badge :priority="$ticket->priority" />
                        @if ($lateSla)
                            <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">SLA lewat</span>
                        @endif
                    </div>

                    <h3 class="text-xl font-semibold text-gray-900">{{ $ticket->subject }}</h3>
                    <p class="mt-4 whitespace-pre-line text-gray-700">{{ $ticket->description }}</p>

                    @if ($ticket->attachments->isNotEmpty())
                        <div class="mt-5 pt-4 border-t">
                            <p class="text-xs font-semibold uppercase text-gray-500 mb-2">Lampiran</p>
                            <ul class="text-sm space-y-1">
                                @foreach ($ticket->attachments as $file)
                                    <li>
                                        <a class="text-indigo-600 hover:underline" target="_blank"
                                           href="{{ asset('storage/' . $file->path) }}">{{ $file->original_name }}</a>
                                        <span class="text-gray-400">({{ number_format($file->size / 1024, 0) }} KB)</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                {{-- Komentar --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                    <h4 class="font-semibold text-gray-800">Komentar ({{ $ticket->comments->count() }})</h4>

                    @forelse ($ticket->comments as $comment)
                        <div class="rounded-lg p-4 border {{ $comment->is_internal ? 'bg-yellow-50 border-yellow-300' : 'bg-gray-50 border-gray-200' }}">
                            <div class="flex items-center gap-2 text-xs text-gray-500 mb-2">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-800 text-white font-semibold">
                                    {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                                </span>
                                <b class="text-gray-700">{{ $comment->user->name }}</b>
                                <span>&middot; {{ $comment->created_at->diffForHumans() }}</span>
                                @if ($comment->is_internal)
                                    <span class="px-2 py-0.5 bg-yellow-200 text-yellow-900 rounded-full font-medium">Internal</span>
                                @endif
                            </div>
                            <p class="whitespace-pre-line text-gray-800">{{ $comment->body }}</p>
                            @foreach ($comment->attachments as $file)
                                <a class="block mt-2 text-sm text-indigo-600 hover:underline" target="_blank"
                                   href="{{ asset('storage/' . $file->path) }}">{{ $file->original_name }}</a>
                            @endforeach
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada komentar.</p>
                    @endforelse

                    <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}"
                          enctype="multipart/form-data" class="space-y-3 pt-4 border-t">
                        @csrf
                        <textarea name="body" rows="3" class="w-full border-gray-300 rounded-md"
                                  placeholder="Tulis komentar...">{{ old('body') }}</textarea>
                        @error('body') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

                        <input type="file" name="attachments[]" multiple class="text-sm">
                        @error('attachments.*') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror

                        @if ($isStaff)
                            <label class="flex items-center gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="is_internal" value="1" class="rounded border-gray-300">
                                Catatan internal (tidak terlihat pelanggan)
                            </label>
                        @endif

                        <button class="px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Kirim Komentar</button>
                    </form>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="space-y-6">

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="font-semibold text-gray-800 mb-4">Detail</h4>
                    <dl class="text-sm space-y-3">
                        <div><dt class="text-gray-500">Pelanggan</dt><dd class="font-medium">{{ $ticket->customer->name }}</dd></div>
                        <div><dt class="text-gray-500">Ditugaskan ke</dt><dd class="font-medium">{{ $ticket->assignee?->name ?? '-' }}</dd></div>
                        <div><dt class="text-gray-500">Kategori</dt><dd class="font-medium">{{ $ticket->category?->name }}</dd></div>
                        <div><dt class="text-gray-500">Dibuat</dt><dd class="font-medium">{{ $ticket->created_at->format('d M Y H:i') }}</dd></div>
                        <div>
                            <dt class="text-gray-500">Batas SLA</dt>
                            <dd class="font-medium {{ $lateSla ? 'text-red-600' : '' }}">{{ $ticket->due_at?->format('d M Y H:i') }}</dd>
                        </div>
                        @if ($ticket->resolved_at)
                            <div><dt class="text-gray-500">Selesai</dt><dd class="font-medium">{{ $ticket->resolved_at->format('d M Y H:i') }}</dd></div>
                        @endif
                    </dl>
                </div>

                @can('update', $ticket)
                    <form method="POST" action="{{ route('tickets.update', $ticket) }}"
                          class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                        @csrf @method('PUT')
                        <h4 class="font-semibold text-gray-800">Kelola Tiket</h4>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Status</label>
                            <select name="status" class="w-full border-gray-300 rounded-md text-sm">
                                @foreach (\App\Enums\TicketStatus::cases() as $s)
                                    <option value="{{ $s->value }}" @selected($ticket->status === $s)>
                                        {{ str($s->value)->replace('_', ' ')->title() }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Prioritas</label>
                            <select name="priority" class="w-full border-gray-300 rounded-md text-sm">
                                @foreach (['low', 'medium', 'high', 'urgent'] as $p)
                                    <option value="{{ $p }}" @selected($ticket->priority === $p)>{{ ucfirst($p) }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Ditugaskan ke</label>
                            <select name="assigned_to" class="w-full border-gray-300 rounded-md text-sm">
                                <option value="">- Belum ditugaskan -</option>
                                @foreach ($staff as $member)
                                    <option value="{{ $member->id }}" @selected($ticket->assigned_to === $member->id)>{{ $member->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <button class="w-full px-4 py-2 bg-gray-800 text-white text-sm rounded-md hover:bg-gray-700">Simpan</button>
                    </form>
                @endcan

                @if ($isStaff)
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h4 class="font-semibold text-gray-800 mb-4">Riwayat Aktivitas</h4>
                        @if ($ticket->logs->isEmpty())
                            <p class="text-sm text-gray-500">Belum ada aktivitas.</p>
                        @else
                            <ol class="relative border-l border-gray-200 ml-2 space-y-5">
                                @foreach ($ticket->logs->sortByDesc('created_at') as $log)
                                    <li class="ml-4">
                                        <span class="absolute -left-1.5 mt-1.5 w-3 h-3 rounded-full bg-indigo-500 border-2 border-white"></span>
                                        <p class="text-xs text-gray-400">{{ $log->created_at->format('d M Y H:i') }}</p>
                                        <p class="text-sm text-gray-800">
                                            <b>{{ $log->user?->name ?? 'Sistem' }}</b>
                                            {{ str_replace('_', ' ', $log->action) }}
                                        </p>
                                        @if ($log->old_value || $log->new_value)
                                            <p class="text-xs text-gray-500">{{ $log->old_value ?? '-' }} &rarr; {{ $log->new_value ?? '-' }}</p>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>