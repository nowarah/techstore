<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreateProductRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 3, max: 255)]
        public readonly string $name,

        #[Assert\NotBlank]
        public readonly string $description,

        #[Assert\Positive]
        public readonly int $price,

        #[Assert\PositiveOrZero]
        public readonly int $stock,

        #[Assert\NotBlank]
        public readonly string $slug,

        #[Assert\NotNull]
        public readonly int $categoryId,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'] ?? '',
            description: $data['description'] ?? '',
            price: $data['price'] ?? 0,
            stock: $data['stock'] ?? 0,
            slug: $data['slug'] ?? '',
            categoryId: $data['categoryId'] ?? 0,
        );
    }
}