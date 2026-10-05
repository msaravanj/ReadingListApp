<div
    x-data="{ book: null, show: false }"
    x-on:open-book-modal.window="book = $event.detail.book; show = true"
    x-on:keydown.escape.window="show = false"
    x-cloak
    x-show="show"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
    role="dialog"
    aria-modal="true"
    x-transition.opacity
>
    <div class="flex max-w-2xl overflow-hidden rounded bg-white shadow-xl" x-on:click.stop>
        <div class="w-1/3">
            <img
                x-bind:src="book?.cover_url"
                x-bind:alt="book?.title"
                class="h-full w-full object-cover"
            >
        </div>
        <div class="flex-1 p-4">
            <button
                type="button"
                class="float-right text-2xl text-gray-500 hover:text-gray-800"
                aria-label="Close modal"
                x-on:click="show = false"
            >&times;</button>
            <p class="text-gray-500">ISBN: <span x-text="book?.isbn"></span></p>
            <h2 class="text-xl font-bold text-gray-800" x-text="book?.title"></h2>
            <p class="text-gray-600" x-text="book?.author"></p>
            <p class="text-gray-700">Rating: <span x-text="book?.rating"></span></p>
            <p class="mt-2 text-gray-700" x-text="book?.notes"></p>
        </div>
    </div>
</div>