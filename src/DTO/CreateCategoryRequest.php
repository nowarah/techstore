<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateCategoryRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 255)]
        public readonly string $name,

        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 255)]
        public readonly string $slug,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            slug: $data['slug'] ?? '',
        );
    }
}
