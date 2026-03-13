<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Category;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function testToArray(): void
    {
        $category = new Category();
        $category->setName('Audio');
        $category->setSlug('audio');

        $product = new Product();
        $product->setName('Headphones');
        $product->setDescription('Noise cancelling');
        $product->setPrice(29999);
        $product->setStock(50);
        $product->setSlug('headphones');
        $product->setCategory($category);
        $product->setCreatedAt(new \DateTimeImmutable('2025-01-15 10:30:00'));

        $array = $product->toArray();

        $this->assertSame('Headphones', $array['name']);
        $this->assertSame('Noise cancelling', $array['description']);
        $this->assertSame(29999, $array['price']);
        $this->assertSame('€299.99', $array['priceFormatted']);
        $this->assertSame(50, $array['stock']);
        $this->assertSame('headphones', $array['slug']);
        $this->assertSame('Audio', $array['category']['name']);
        $this->assertSame('2025-01-15 10:30:00', $array['createdAt']);
    }
}
