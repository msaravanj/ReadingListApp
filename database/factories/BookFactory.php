<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\Sequence;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return $this->realBookData()[0];
    }

    /**
     * Use deterministic books instead of generated placeholder data.
     */
    public function realBooks(): static
    {
        return $this->state(new Sequence(...$this->realBookData()));
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function realBookData(): array
    {
        return [
            [
                'title' => 'The Hobbit',
                'author' => 'J. R. R. Tolkien',
                'status' => 'read',
                'rating' => 5,
                'notes' => 'A classic fantasy adventure about Bilbo Baggins.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780261102217-M.jpg',
                'isbn' => '9780261102217',
            ],
            [
                'title' => 'Pride and Prejudice',
                'author' => 'Jane Austen',
                'status' => 'reading',
                'rating' => 5,
                'notes' => 'A novel about manners, family, and first impressions.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780141439518-M.jpg',
                'isbn' => '9780141439518',
            ],
            [
                'title' => '1984',
                'author' => 'George Orwell',
                'status' => 'want_to_read',
                'rating' => null,
                'notes' => 'A dystopian novel about surveillance and authoritarian power.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780141036144-M.jpg',
                'isbn' => '9780141036144',
            ],
            [
                'title' => 'The Name of the Rose',
                'author' => 'Umberto Eco',
                'status' => 'reading',
                'rating' => 4,
                'notes' => 'A historical mystery set in a fourteenth-century monastery.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780156001311-M.jpg',
                'isbn' => '9780156001311',
            ],
            [
                'title' => 'Clean Code',
                'author' => 'Robert C. Martin',
                'status' => 'want_to_read',
                'rating' => null,
                'notes' => 'Practical principles for writing readable and maintainable code.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780132350884-M.jpg',
                'isbn' => '9780132350884',
            ],
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'status' => 'read',
                'rating' => 4,
                'notes' => 'A story of wealth, ambition, and lost love in the Jazz Age.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780141182636-M.jpg',
                'isbn' => '9780141182636',
            ],
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'status' => 'read',
                'rating' => 5,
                'notes' => 'A coming-of-age story about justice, empathy, and prejudice.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780061120084-M.jpg',
                'isbn' => '9780061120084',
            ],
            [
                'title' => 'Dune',
                'author' => 'Frank Herbert',
                'status' => 'reading',
                'rating' => 5,
                'notes' => 'An epic science-fiction novel set on the desert planet Arrakis.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780441172719-M.jpg',
                'isbn' => '9780441172719',
            ],
            [
                'title' => 'The Little Prince',
                'author' => 'Antoine de Saint-Exupery',
                'status' => 'want_to_read',
                'rating' => null,
                'notes' => 'A poetic tale about friendship, love, and seeing beyond appearances.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780156012195-M.jpg',
                'isbn' => '9780156012195',
            ],
            [
                'title' => 'Sapiens',
                'author' => 'Yuval Noah Harari',
                'status' => 'reading',
                'rating' => 4,
                'notes' => 'An exploration of the history and development of humankind.',
                'cover_image' => 'https://covers.openlibrary.org/b/isbn/9780062316097-M.jpg',
                'isbn' => '9780062316097',
            ],
        ];
    }
}
