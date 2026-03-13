<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\CartItem;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    public function __construct(
        private EntityManagerInterface $em,
        private CartRepository $cartRepository,
        private ProductRepository $productRepository,
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
        } elseif ($user && !$cart->getUser()) {
            $cart->setUser($user);
            $this->em->flush();
        }

        return $cart;
    }

    public function addItemByProductId(Cart $cart, int $productId, int $quantity = 1): void
    {
        $product = $this->productRepository->find($productId);
        if (!$product) {
            throw new \RuntimeException('Product not found');
        }

        $this->addItem($cart, $product, $quantity);
    }

    public function addItem(Cart $cart, Product $product, int $quantity = 1): void
    {
        if ($quantity < 1) {
            throw new \RuntimeException('Quantity must be at least 1');
        }

        $existingQuantity = 0;
        foreach ($cart->getItems() as $item) {
            if ($item->getProduct() === $product) {
                $existingQuantity = $item->getQuantity();
                break;
            }
        }

        if ($product->getStock() < $existingQuantity + $quantity) {
            throw new \RuntimeException('Insufficient stock');
        }

        $cart->addItem(
            (new CartItem())
                ->setProduct($product)
                ->setQuantity($quantity)
        );

        $this->em->flush();
    }

    public function removeItem(Cart $cart, int $itemId): bool
    {
        foreach ($cart->getItems() as $item) {
            if ($item->getId() === $itemId) {
                $cart->getItems()->removeElement($item);
                $this->em->remove($item);
                $this->em->flush();
                return true;
            }
        }
        return false;
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
