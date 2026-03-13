<?php

namespace App\Service;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Enum\OrderStatus;
use App\Service\CartService;
use App\Service\ProductService;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CartService $cartService,
        private ProductService $productService,
    ) {}

    public function createOrderFromCart(User $user): Order
    {
        $cart = $this->cartService->getOrCreateCart($user);

        if ($cart->getItems()->isEmpty()) {
            throw new \RuntimeException('Cart is empty');
        }

        $this->em->beginTransaction();

        try {
            $order = new Order();
            $order->setUser($user);
            $order->setStatus(OrderStatus::Pending);
            $order->setTotal($cart->getTotal());

            foreach ($cart->getItems() as $cartItem) {
                $this->productService->validateAndDecreaseStock($cartItem->getProduct(), $cartItem->getQuantity());

                $orderItem = new OrderItem();
                $orderItem->setProduct($cartItem->getProduct());
                $orderItem->setQuantity($cartItem->getQuantity());
                $orderItem->setPrice($cartItem->getProduct()->getPrice());
                $orderItem->setOrder($order);

                $this->em->persist($orderItem);
            }

            $this->em->persist($order);
            $this->cartService->clearCartItems($cart);

            $this->em->flush();
            $this->em->commit();

            return $order;

        } catch (\Exception $e) {
            $this->em->rollback();
            throw $e;
        }
    }
}