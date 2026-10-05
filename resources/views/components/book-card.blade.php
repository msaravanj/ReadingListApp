@props(['book'])

<div
    class="flex gap-20 p-4 border rounded shadow-md bg-white hover:bg-gray-400 duration-500 border-gray-300 hover:border-gray-600 cursor-pointer scale-100 hover:scale-105 transition-transform"
    x-on:click="$dispatch('open-book-modal', { book: @js([
        'cover_url' => $book->cover_url,
        'title' => $book->title,
        'isbn' => $book->isbn,
        'author' => $book->author,
        'rating' => $book->rating,
        'notes' => $book->notes,
    ]) })"
>
    <div class="w-32 h-48 overflow-hidden rounded shadow-md">
        <img src="{{ $book->cover_url }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
    </div>
    <div class="flex flex-col items-center justify-center p-4">

        <h3 class="text-gray-800 text-lg font-semibold">{{ $book->title }}</h3>
        <p class="text-gray-600">{{ $book->author }}</p>
    </div>
</div>