<div x-data="{ book: null, show: false }" x-on:open-book-modal.window="book = $event.detail.book; show = true"
    x-on:keydown.escape.window="show = false" x-cloak x-show="show"
    class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-black/70 p-4 backdrop-blur-sm"
    role="dialog" aria-modal="true" x-on:click.self="show = false" x-transition.opacity>
    <div class="relative flex max-h-[calc(100vh-2rem)] w-full max-w-3xl flex-col overflow-y-auto rounded-2xl border border-library-border bg-library-bg shadow-2xl sm:flex-row"
        x-on:click.stop>
        <div class="flex shrink-0 justify-center bg-black/20 p-6 sm:w-2/5 sm:p-8">
            <img x-bind:src="book?.cover_image" x-bind:alt="book?.title"
                class="h-72 w-48 rounded-lg object-cover shadow-xl sm:h-96 sm:w-64">
        </div>
        <div class="flex min-w-0 flex-1 flex-col gap-6 p-6 sm:p-8">
            <div class="border-b border-library-border pb-5 pr-8">
                <p class="mb-2 text-xs font-semibold uppercase tracking-widest text-gray-500">Book details</p>
                <h2 class="text-2xl font-bold text-white sm:text-3xl" x-text="book?.title"></h2>
                <p class="mt-2 text-gray-400" x-text="book?.author"></p>
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div class="rounded-lg bg-library-surface p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status</p>
                    <p class="mt-1 font-medium text-library-cream"
                        x-text="{ want_to_read: 'Want to Read', reading: 'Reading', read: 'Read' }[book?.status] ?? 'Unknown'">
                    </p>
                </div>
                <div class="rounded-lg bg-library-surface p-4">
                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Rating</p>
                    <div class="mt-1 flex items-center gap-1" role="group" aria-label="Book rating">
                        <template x-for="rating in [1, 2, 3, 4, 5]" :key="rating">
                            <button type="button"
                                x-on:click="if (await $wire.updateBookRating(book.id, rating)) book.rating = rating"
                                x-bind:class="rating <= (book?.rating ?? 0) ? 'text-yellow-400' : 'text-gray-600'"
                                class="text-xl transition hover:text-yellow-300"
                                x-bind:aria-label="`Rate ${rating} out of 5`"
                                x-bind:aria-pressed="rating === book?.rating">
                                <span aria-hidden="true">&starf;</span>
                            </button>
                        </template>
                        <span class="ml-1 text-sm text-gray-400" x-text="book?.rating ?? 'Not rated'"></span>
                    </div>
                </div>
            </div>
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">ISBN</p>
                <div class="flex flex-wrap gap-2">
                    <span class="rounded bg-library-surface px-3 py-2 text-sm text-gray-300"
                        x-text="book?.isbn ?? 'N/A'"></span>
                </div>
            </div>
            <div>
                <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500">Notes</p>
                <p class="whitespace-pre-line text-sm leading-6 text-gray-300"
                    x-text="book?.notes || 'No notes available.'"></p>
            </div>
        </div>
        <button type="button"
            class="absolute right-4 top-4 rounded-full p-2 text-2xl leading-none text-gray-400 hover:bg-white/10 hover:text-white"
            aria-label="Close modal" x-on:click="show = false">&times;</button>
    </div>
</div>
