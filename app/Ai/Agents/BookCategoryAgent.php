<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;
use UnexpectedValueException;

class BookCategoryAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): Stringable|string
    {
        return 'You classify books using only the categories provided in the prompt.';
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'categories' => $schema->array()
                ->min(1)
                ->max(3)
                ->items($schema->string())
                ->required(),
        ];
    }

    /**
     * @param  list<string>  $availableCategories
     * @return array{categories: list<string>}
     */
    public function generateCategories(string $title, string $author, ?string $description, array $availableCategories): array
    {
        $response = $this->prompt(
            "Classify the book titled '{$title}' by {$author}. "
            .'Choose one to three categories only from this list: '
            .implode(', ', $availableCategories)
            .". Description: {$description}"
        )->toArray();
        $categories = $response['categories'] ?? null;

        if (! is_array($categories) || $categories === []) {
            throw new UnexpectedValueException('The book category agent returned an invalid category list.');
        }

        $normalizedCategories = [];

        foreach ($categories as $category) {
            if (! is_string($category) || trim($category) === '') {
                throw new UnexpectedValueException('The book category agent returned an invalid category.');
            }

            $normalizedCategories[] = trim($category);
        }

        return ['categories' => array_values(array_unique($normalizedCategories))];
    }
}
