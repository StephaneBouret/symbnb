<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class ContainsMinImages extends Constraint
{
    public string $message = 'Vous devez ajouter au moins {{ limit }} images.';
    public $limit = 5;

    // all configurable options must be passed to the constructor
    public function __construct(?string $limit = null, ?string $message = null, ?array $groups = null, $payload = null)
    {
        parent::__construct([], $groups, $payload);

        $this->limit = $limit ?? $this->limit;
        $this->message = $message ?? $this->message;
    }
}