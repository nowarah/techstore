<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    public function testAddItemNew(): void
    {
        $cart = new Cart();
        $product = new Product();

        $item = new CartItem();
        $item->setProduct($product);
        $item->setQuantity(3);

        $cart->addItem($item);

        $this->assertCount(1, $cart->getItems());
        $this->assertSame(3, $cart->getItems()->first()->getQuantity());
        $this->assertSame($cart, $item->getCart());
    }

    public function testAddItemMergesQuantity(): void
    {
        $cart = new Cart();
        $product = new Product();

        $item1 = new CartItem();
        $item1->setProduct($product);
        $item1->setQuantity(2);
        $cart->addItem($item1);

        $item2 = new CartItem();
        $item2->setProduct($product);
        $item2->setQuantity(3);
        $cart->addItem($item2);

        $this->assertCount(1, $cart->getItems());
        $this->assertSame(5, $cart->getItems()->first()->getQuantity());
    }

    public function testGetTotal(): void
    {
        $cart = new Cart();

        $product1 = new Product();
        $product1->setPrice(1000);
        $item1 = new CartItem();
        $item1->setProduct($product1);
        $item1->setQuantity(2);
        $cart->addItem($item1);

        $product2 = new Product();
        $product2->setPrice(500);
        $item2 = new CartItem();
        $item2->setProduct($product2);
        $item2->setQuantity(3);
        $cart->addItem($item2);

        // (1000 * 2) + (500 * 3) = 3500
        $this->assertSame(3500, $cart->getTotal());
    }
}
