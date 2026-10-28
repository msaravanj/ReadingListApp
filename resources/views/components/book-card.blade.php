@props(['book'])

<div
    class="group relative flex h-full min-h-80 w-full min-w-0 flex-col overflow-hidden rounded-xl border border-library-border bg-library-surface shadow-md transition duration-300 hover:-translate-y-1 hover:border-gray-600 hover:shadow-xl">
    <div class="relative h-56 shrink-0 overflow-hidden bg-black/20">
        <img src="{{ $book->cover_image }}" alt="{{ $book->title }}"
            class="h-full w-full object-cover transition duration-500 group-hover:scale-105">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
        <button type="button" title="View Details"
            class="absolute right-3 top-3 rounded-full bg-black/40 p-2 text-gray-200 backdrop-blur-sm transition hover:bg-black/70 hover:text-white"
            x-on:click.stop="$dispatch('open-book-modal', { book: @js([
    'id' => $book->id,
    'cover_image' => $book->cover_image,
    'title' => $book->title,
    'isbn' => $book->isbn,
    'author' => $book->author,
    'status' => $book->status->value,
    'rating' => $book->rating,
    'notes' => $book->notes,
]) })">
            <x-icon name="arrows-pointing-out" class="h-4 w-4" />
        </button>
    </div>
    <div class="flex min-w-0 flex-1 flex-col gap-4 p-4">
        <div class="flex items-center justify-between gap-2">
            <span class="rounded-full bg-black/20 px-2.5 py-1 text-xs font-medium text-gray-300">
                {{ $book->status->value === \App\Enums\BookStatus::WANT_TO_READ->value ? 'Want to Read' : ($book->status->value === \App\Enums\BookStatus::READING->value ? 'Reading' : 'Read') }}
            </span>
            <span class="flex items-center gap-1 text-sm text-yellow-400">
                <x-icon name="star" class="h-4 w-4" />
                <span class="text-gray-300">{{ $book->rating ?? '—' }}</span>
            </span>
        </div>
        <div class="flex min-h-16 flex-col gap-1">
            <h3 class="line-clamp-2 text-lg font-semibold leading-6 text-white">{{ $book->title }}</h3>
            <p class="line-clamp-2 text-sm text-gray-400 italic">{{ $book->author }}</p>
        </div>
        <div class="mt-auto flex items-center justify-between border-t border-library-border pt-3">
            <span class="text-xs text-gray-400">ISBN: {{ $book->isbn ?? 'N/A' }}</span>
        </div>
    </div>
</div>
