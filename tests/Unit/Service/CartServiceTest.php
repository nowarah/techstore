<?php

namespace App\Tests\Unit\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[AllowMockObjectsWithoutExpectations]
class CartServiceTest extends TestCase
{
    private MockObject&EntityManagerInterface $em;
    private MockObject&CartRepository $cartRepository;
    private MockObject&ProductRepository $productRepository;
    private RequestStack&\PHPUnit\Framework\MockObject\Stub $requestStack;
    private CartService $service;

    protected function setUp(): void
    {
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->cartRepository = $this->createMock(CartRepository::class);
        $this->productRepository = $this->createMock(ProductRepository::class);
        $this->requestStack = $this->createStub(RequestStack::class);

        $session = $this->createStub(SessionInterface::class);
        $session->method('getId')->willReturn('session-123');
        $this->requestStack->method('getSession')->willReturn($session);

        $this->service = new CartService($this->em, $this->cartRepository, $this->productRepository, $this->requestStack);
    }

    public function testGetOrCreateCartExisting(): void
    {
        $existingCart = new Cart();
        $this->cartRepository->expects(self::any())->method('findOneBy')
            ->with(['sessionId' => 'session-123'])
            ->willReturn($existingCart);

        $this->em->expects(self::never())->method('persist');

        $cart = $this->service->getOrCreateCart();

        $this->assertSame($existingCart, $cart);
    }

    public function testGetOrCreateCartNew(): void
    {
        $this->cartRepository->expects(self::any())->method('findOneBy')->willReturn(null);

        $this->em->expects(self::once())->method('persist')->with(self::isInstanceOf(Cart::class));
        $this->em->expects(self::once())->method('flush');

        $cart = $this->service->getOrCreateCart();

        $this->assertSame('session-123', $cart->getSessionId());
    }

    public function testGetOrCreateCartWithUser(): void
    {
        $this->cartRepository->expects(self::any())->method('findOneBy')->willReturn(null);
        $this->em->expects(self::any())->method('persist');
        $this->em->expects(self::any())->method('flush');

        $user = new User();
        $cart = $this->service->getOrCreateCart($user);

        $this->assertSame($user, $cart->getUser());
    }

    public function testAddItem(): void
    {
        $product = new Product();
        $product->setStock(10);

        $cart = new Cart();

        $this->em->expects(self::once())->method('flush');

        $this->service->addItem($cart, $product, 2);

        $this->assertCount(1, $cart->getItems());
        $this->assertSame(2, $cart->getItems()->first()->getQuantity());
    }

    public function testAddItemInsufficientStock(): void
    {
        $product = new Product();
        $product->setStock(1);

        $cart = new Cart();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Insufficient stock');

        $this->service->addItem($cart, $product, 5);
    }

    public function testRemoveItem(): void
    {
        $cart = new Cart();
        $product = new Product();
        $product->setStock(10);

        $this->service->addItem($cart, $product, 1);
        $item = $cart->getItems()->first();

        $ref = new \ReflectionProperty(CartItem::class, 'id');
        $ref->setValue($item, 42);

        $this->em->expects(self::once())->method('remove')->with($item);

        $this->service->removeItem($cart, 42);

        $this->assertCount(0, $cart->getItems());
    }

    public function testClearCart(): void
    {
        $cart = new Cart();
        $product = new Product();
        $product->setStock(10);

        $this->service->addItem($cart, $product, 1);
        $this->assertCount(1, $cart->getItems());

        $this->em->expects(self::atLeastOnce())->method('remove');
        $this->em->expects(self::atLeastOnce())->method('flush');

        $this->service->clearCart($cart);

        $this->assertCount(0, $cart->getItems());
    }

    public function testClearCartItems(): void
    {
        $cart = new Cart();
        $product = new Product();
        $product->setStock(10);

        $this->service->addItem($cart, $product, 1);

        $this->em->expects(self::atLeastOnce())->method('remove');

        $this->service->clearCartItems($cart);

        $this->assertCount(0, $cart->getItems());
    }
}
