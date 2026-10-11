<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">Buat Tiket</h2>
    </x-slot>

    <div class="py-8 max-w-2xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('tickets.store') }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Subjek</label>
                <input name="subject" value="{{ old('subject') }}"
                       class="mt-1 w-full border-gray-300 rounded-md">
                @error('subject') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Kategori</label>
                <select name="category_id" class="mt-1 w-full border-gray-300 rounded-md">
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Prioritas</label>
                <select name="priority" class="mt-1 w-full border-gray-300 rounded-md">
                    @foreach (['low', 'medium', 'high', 'urgent'] as $p)
                        <option value="{{ $p }}" @selected(old('priority', 'medium') === $p)>{{ $p }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Deskripsi</label>
                <textarea name="description" rows="5"
                          class="mt-1 w-full border-gray-300 rounded-md">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-600 text-sm">{{ $message }}</p> @enderror
            </div>

            <button class="px-4 py-2 bg-gray-800 text-white rounded-md">Kirim</button>
        </form>
    </div>
</x-app-layout>