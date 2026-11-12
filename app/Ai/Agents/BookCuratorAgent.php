<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;
use UnexpectedValueException;

class BookCuratorAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are an expert literary curator. Your job is to discover newly released, popular books across various genres. Always return exactly 10 books.';
    }

    /**
     * Get the agent's structured output schema definition.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'books' => $schema->array()
                ->min(10)
                ->max(10)
                ->items($schema->object([
                    'title' => $schema->string()
                        ->description('The exact title of the book.')
                        ->required(),
                    'author' => $schema->string()
                        ->description('The full name of the author.')
                        ->required(),
                ]))
                ->required(),
        ];
    }

    /**
     * Find newly released popular books.
     *
     * @return array{books: list<array{title: string, author: string}>}
     */
    public function findNewReleases(): array
    {
        $response = $this->prompt('Give me exactly 10 newly released popular books.')->toArray();
        $books = $response['books'] ?? null;

        if (! is_array($books) || count($books) !== 10) {
            throw new UnexpectedValueException('The book curator returned an invalid number of books.');
        }

        $normalizedBooks = [];

        foreach ($books as $book) {
            if (
                ! is_array($book)
                || ! is_string($book['title'] ?? null)
                || ! is_string($book['author'] ?? null)
            ) {
                throw new UnexpectedValueException('The book curator returned an invalid book structure.');
            }

            $normalizedBooks[] = [
                'title' => $book['title'],
                'author' => $book['author'],
            ];
        }

        return ['books' => $normalizedBooks];
    }
}
