<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;
use UnexpectedValueException;

class BookDescriptionAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are an expert literary curator. Your job is to provide a brief description for a given book.';
    }

    /**
     * Get the agent's structured output schema definition.
     *
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'description' => $schema->string()
                ->description('A brief description of the book.')
                ->required(),
        ];
    }

    /**
     * Get a brief description for a given book.
     *
     * @return array{description: string}
     */
    public function generateDescription(string $title, string $author): array
    {
        $response = $this->prompt("Provide a brief description in 2-3 sentences for the book titled '{$title}' by {$author}.")->toArray();
        $description = $response['description'] ?? null;

        if (! is_string($description)) {
            throw new UnexpectedValueException('The book description agent returned an invalid description.');
        }

        return ['description' => $description];
    }
}
