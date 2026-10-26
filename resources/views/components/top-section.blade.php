@props(['foundBooks' => [], 'search' => ''])

<div class="w-full">
    <nav class="relative z-10 flex h-16 w-full items-center justify-between px-4 sm:px-12">
        <a href="/" class="text-white weight-400 hover:text-gray-300">Reading List</a>
        <a href="/admin" class="text-white weight-400 hover:text-gray-300">Admin</a>
    </nav>
    <div class="relative flex min-h-64 w-full max-w-full px-4 py-2 text-white">
        <div class="relative z-10 flex w-full max-w-xl flex-col gap-8 p-4 sm:p-12">
            <div class="flex flex-col gap-2">
                <p class="text-xs">YOUR PERSONAL LIBRARY</p>
                <h1 class="text-3xl font-bold">Every book has a story.</h1>
            </div>
            <form class="z-10 flex flex-col gap-2" wire:submit.prevent="fetchBooks">
                <label for="search">Find your next favorite, revisit a classic, and make time to read.</label>
                <x-input class="bg-library-bg border rounded-md outline-#303730 px-4 py-2 placeholder:text-gray-500"
                    type="text" placeholder="Search for books..." id="search" icon="magnifying-glass"
                    name="search" wire:model="search">
                    <x-slot:suffix button>
                        <span wire:loading wire:target="fetchBooks">
                            <x-spinner lg gradient />
                        </span>
                        <span wire:loading.remove wire:target="fetchBooks">
                            <x-button type="submit" icon="arrow-right" class="hover:bg-gray-700 active:bg-gray-700" />
                        </span>
                    </x-slot:suffix>
                </x-input>
            </form>
            @if (count($foundBooks) > 0)
                <div
                    class="relative z-20 flex max-h-96 flex-col gap-2 overflow-y-auto rounded-lg border border-library-border bg-library-bg/95 p-3 shadow-xl backdrop-blur-sm">
                    @foreach ($foundBooks as $index => $book)
                        <div wire:key="search-result-{{ $index }}" x-data="{ saving: false }"
                            class="flex items-center gap-3 rounded-md bg-library-surface p-2">
                            <img src="{{ app(\App\Services\OpenLibraryService::class)->coverUrl($book['cover_i'] ?? null) }}"
                                alt="{{ $book['title'] ?? 'Book cover' }}"
                                class="h-16 w-11 shrink-0 rounded object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-white">
                                    {{ $book['title'] ?? 'Untitled' }}
                                </p>
                                <p class="truncate text-xs text-gray-400">
                                    {{ !empty($book['author_name']) ? implode(', ', $book['author_name']) : 'Unknown Author' }}
                                </p>
                            </div>
                            <div x-show="saving" x-cloak class="shrink-0">
                                <x-spinner lg gradient />
                            </div>
                            <div x-show="!saving" class="shrink-0">
                                <x-button
                                    x-on:click="saving = true; $wire.addBook({{ $index }}).finally(() => saving = false)"
                                    class="rounded-md bg-white px-3 py-1.5 text-xs font-semibold text-library-bg transition hover:bg-gray-200">
                                    Save
                                </x-button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif (strlen($search ?? '') >= 3)
                <p
                    class="relative z-20 rounded-lg border border-library-border bg-library-bg/95 p-3 text-sm text-gray-400">
                    No books found.
                </p>
            @endif
        </div>
        <div class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat mask-l-from-20% mask-l-to-60%"
            style="background-image: url('{{ asset('images/booksImage.jpg') }}');"></div>
    </div>
</div>
