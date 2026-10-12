@props(['book'])

<div class="flex w-full max-w-xl flex-col gap-4 rounded border border-gray-300 p-4 shadow-md transition-transform duration-500 hover:scale-105 hover:border-gray-600 hover:bg-gray-800 cursor-pointer sm:flex-row sm:justify-between"
    x-on:click="$dispatch('open-book-modal', { book: @js([
    'cover_image' => $book->cover_image,
    'title' => $book->title,
    'isbn' => $book->isbn,
    'author' => $book->author,
    'rating' => $book->rating,
    'notes' => $book->notes,
]) })">
    <div class="mx-auto h-48 w-32 shrink-0 overflow-hidden rounded shadow-md sm:mx-0">
        <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
    </div>
    <div
        class="flex min-w-0 flex-1 flex-col items-center justify-center p-2 text-center sm:items-end sm:p-4 sm:text-right">
        <h3 class="w-full wrap-break-words text-lg font-semibold text-white">{{ $book->title }}</h3>
        <p class="w-full wrap-break-words text-gray-400 italic">{{ $book->author }}</p>
    </div>
</div>
