<?php

namespace App\Tests\Unit\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use App\Service\CartService;
use App\Service\OrderService;
use App\Service\ProductService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

#[AllowMockObjectsWithoutExpectations]
class OrderServiceTest extends TestCase
{
    private MockObject&EntityManagerInterface $em;
    private MockObject&CartService $cartService;
    private MockObject&ProductService $productService;
    private OrderService $service;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->cartService = $this->createMock(CartService::class);
        $this->productService = $this->createMock(ProductService::class);
        $this->service = new OrderService($this->em, $this->cartService, $this->productService);
    }

    private function createCartWithItem(int $price = 1000, int $quantity = 2): Cart
    {
        $product = new Product();
        $product->setPrice($price);
        $product->setStock(10);

        $item = new CartItem();
        $item->setProduct($product);
        $item->setQuantity($quantity);

        $cart = new Cart();
        $cart->addItem($item);

        return $cart;
    }

    public function testCreateOrderFromCart(): void
    {
        $user = new User();
        $cart = $this->createCartWithItem(1000, 2);

        $this->cartService->expects(self::any())->method('getOrCreateCart')->with($user)->willReturn($cart);

        $this->em->expects(self::once())->method('beginTransaction');
        $this->em->expects(self::once())->method('commit');
        $this->em->expects(self::never())->method('rollback');
        $this->em->expects(self::once())->method('flush');

        $this->productService->expects(self::once())->method('validateAndDecreaseStock');
        $this->cartService->expects(self::once())->method('clearCartItems')->with($cart);

        $order = $this->service->createOrderFromCart($user);

        $this->assertSame(2000, $order->getTotal());
        $this->assertSame($user, $order->getUser());
    }

    public function testCreateOrderFromCartEmptyCart(): void
    {
        $user = new User();
        $cart = new Cart();

        $this->cartService->expects(self::any())->method('getOrCreateCart')->willReturn($cart);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Cart is empty');

        $this->service->createOrderFromCart($user);
    }

    public function testCreateOrderFromCartRollbackOnError(): void
    {
        $user = new User();
        $cart = $this->createCartWithItem();

        $this->cartService->expects(self::any())->method('getOrCreateCart')->willReturn($cart);
        $this->productService->expects(self::any())->method('validateAndDecreaseStock')
            ->willThrowException(new \RuntimeException('Insufficient stock'));

        $this->em->expects(self::once())->method('beginTransaction');
        $this->em->expects(self::once())->method('rollback');
        $this->em->expects(self::never())->method('commit');

        $this->expectException(\RuntimeException::class);

        $this->service->createOrderFromCart($user);
    }
}
