<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
use App\Trait\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/cart')]
class CartController extends AbstractController
{
    use ApiResponseTrait;

    #[Route('', methods: ['GET'])]
    public function index(CartService $cartService): JsonResponse
    {
        $cart = $cartService->getOrCreateCart($this->getUser());
        return $this->success($cart->toArray());
    }

    #[Route('/add', methods: ['POST'])]
    public function add(Request $request, CartService $cartService, ProductRepository $productRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true) ?? [];

        $product = $productRepository->find($data['product_id'] ?? 0);
        if (!$product) {
            return $this->error('Product not found', 404);
        }

        try {
            $cart = $cartService->getOrCreateCart($this->getUser());
            $cartService->addItem($cart, $product, $data['quantity'] ?? 1);
            return $this->success($cart->toArray());
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[Route('/remove/{itemId}', methods: ['DELETE'])]
    public function remove(int $itemId, CartService $cartService): JsonResponse
    {
        $cart = $cartService->getOrCreateCart($this->getUser());
        $cartService->removeItem($cart, $itemId);

        return $this->success($cart->toArray());
    }

    #[Route('/clear', methods: ['DELETE'])]
    public function clear(CartService $cartService): JsonResponse
    {
        $cart = $cartService->getOrCreateCart($this->getUser());
        $cartService->clearCart($cart);

        return $this->success(['message' => 'Cart cleared']);
    }
}