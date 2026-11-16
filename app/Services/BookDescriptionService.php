<?php

namespace App\Services;

use App\Ai\Agents\BookDescriptionAgent;
use UnexpectedValueException;

class BookDescriptionService
{
    public function __construct(private BookDescriptionAgent $agent) {}

    public function fillIfEmpty(string $title, string $author, ?string $notes): string
    {
        if (filled(trim($notes ?? ''))) {
            return $notes;
        }

        $response = $this->agent->generateDescription($title, $author);
        $description = $response['description'];

        if (blank($description)) {
            throw new UnexpectedValueException('The book description agent returned an empty description.');
        }

        return trim($description);
    }
}
