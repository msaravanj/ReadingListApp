@props(['book'])

<div class="flex justify-between p-4 border rounded shadow-md hover:bg-gray-800 duration-500 border-gray-300 hover:border-gray-600 cursor-pointer scale-100 hover:scale-105 transition-transform"
    x-on:click="$dispatch('open-book-modal', { book: @js([
    'cover_image' => $book->cover_image,
    'title' => $book->title,
    'isbn' => $book->isbn,
    'author' => $book->author,
    'rating' => $book->rating,
    'notes' => $book->notes,
]) })">
    <div class="object-left w-32 h-48 overflow-hidden rounded shadow-md">
        <img src="{{ $book->cover_image }}" alt="{{ $book->title }}" class="w-full h-full object-cover">
    </div>
    <div class="object-right flex flex-col items-center justify-center p-4">
        <h3 class="text-white text-lg font-semibold">{{ $book->title }}</h3>
        <p class="text-gray-400 italic">{{ $book->author }}</p>
    </div>
</div>
