<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CartRepository $cartRepository,
        private RequestStack $requestStack,
    ) {}

    public function getOrCreateCart(?User $user = null): Cart
    {
        $session = $this->requestStack->getSession();
        $sessionId = $session->getId();

        $cart = $this->cartRepository->findOneBy(['sessionId' => $sessionId]);

        if (!$cart) {
            $cart = new Cart();
            $cart->setSessionId($sessionId);
            if ($user) {
                $cart->setUser($user);
            }
            $this->em->persist($cart);
            $this->em->flush();
        }

        return $cart;
    }

    public function addItem(Cart $cart, Product $product, int $quantity = 1): void
    {
        if ($product->getStock() < $quantity) {
            throw new \RuntimeException('Insufficient stock');
        }

        $cart->addItem(
            (new CartItem())
                ->setProduct($product)
                ->setQuantity($quantity)
        );

        $this->em->flush();
    }

    public function removeItem(Cart $cart, int $itemId): void
    {
        foreach ($cart->getItems() as $item) {
            if ($item->getId() === $itemId) {
                $cart->getItems()->removeElement($item);
                $this->em->remove($item);
                break;
            }
        }
        $this->em->flush();
    }

    public function clearCartItems(Cart $cart): void
    {
        foreach ($cart->getItems() as $item) {
            $this->em->remove($item);
        }
        $cart->getItems()->clear();
    }

    public function clearCart(Cart $cart): void
    {
        $this->clearCartItems($cart);
        $this->em->flush();
    }

}