<?php

namespace App\Service;

use App\DTO\CreateProductRequest;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CategoryRepository $categoryRepository,
    ) {}

    public function createProduct(CreateProductRequest $dto): Product
    {
        $category = $this->categoryRepository->find($dto->categoryId);

        if (!$category) {
            throw new \InvalidArgumentException('Category not found');
        }

        $product = new Product();
        $product->setName($dto->name);
        $product->setDescription($dto->description);
        $product->setPrice($dto->price);
        $product->setStock($dto->stock);
        $product->setSlug($dto->slug);
        $product->setCategory($category);

        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    public function decreaseStock(Product $product, int $quantity): void
    {
        $this->validateStock($product, $quantity);
        $product->setStock($product->getStock() - $quantity);
        $this->em->flush();
    }

    public function validateAndDecreaseStock(Product $product, int $quantity): void
    {
        $this->validateStock($product, $quantity);
        $product->setStock($product->getStock() - $quantity);
    }

    private function validateStock(Product $product, int $quantity): void
    {
        if ($product->getStock() < $quantity) {
            throw new \RuntimeException('Insufficient stock');
        }
    }

}