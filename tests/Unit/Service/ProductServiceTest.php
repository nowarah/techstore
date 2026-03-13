<?php

namespace App\Tests\Unit\Service;

use App\DTO\CreateProductRequest;
use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class ProductServiceTest extends TestCase
{
    private MockObject&EntityManagerInterface $em;
    private CategoryRepository $categoryRepository;
    private ProductService $service;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->categoryRepository = $this->createStub(CategoryRepository::class);
        $this->service = new ProductService($this->em, $this->categoryRepository);
    }

    public function testCreateProduct(): void
    {
        $category = new Category();
        $this->categoryRepository->method('find')->willReturn($category);

        $this->em->expects(self::once())->method('persist')->with(self::isInstanceOf(Product::class));
        $this->em->expects(self::once())->method('flush');

        $dto = new CreateProductRequest('Test Product', 'A description', 1999, 10, 'test-product', 1);

        $product = $this->service->createProduct($dto);

        $this->assertSame('Test Product', $product->getName());
        $this->assertSame(1999, $product->getPrice());
        $this->assertSame(10, $product->getStock());
        $this->assertSame('test-product', $product->getSlug());
        $this->assertSame($category, $product->getCategory());
    }

    public function testCreateProductCategoryNotFound(): void
    {
        $this->categoryRepository->method('find')->willReturn(null);

        $dto = new CreateProductRequest('Test', 'Desc', 1000, 5, 'test', 999);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Category not found');

        $this->service->createProduct($dto);
    }

    public function testDecreaseStock(): void
    {
        $product = new Product();
        $product->setStock(10);

        $this->em->expects(self::once())->method('flush');

        $this->service->decreaseStock($product, 3);

        $this->assertSame(7, $product->getStock());
    }

    public function testDecreaseStockInsufficientStock(): void
    {
        $product = new Product();
        $product->setStock(2);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Insufficient stock');

        $this->service->decreaseStock($product, 5);
    }

    public function testValidateAndDecreaseStockNoFlush(): void
    {
        $product = new Product();
        $product->setStock(10);

        $this->em->expects(self::never())->method('flush');

        $this->service->validateAndDecreaseStock($product, 3);

        $this->assertSame(7, $product->getStock());
    }
}
