<?php

namespace App\Controller;

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

    public function __construct(
        private CartService $cartService,
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($this->getUser());
        return $this->success($cart->toArray());
    }

    #[Route('/add', methods: ['POST'])]
    public function add(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if (!is_array($data)) {
            return $this->error('Invalid JSON payload', 400);
        }

        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;

        if (!$productId) {
            return $this->error('Product ID is required', 400);
        }

        if (!is_int($quantity) || $quantity < 1) {
            return $this->error('Quantity must be a positive integer', 400);
        }

        try {
            $cart = $this->cartService->getOrCreateCart($this->getUser());
            $this->cartService->addItemByProductId($cart, $productId, $quantity);
            return $this->success($cart->toArray());
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }

    #[Route('/remove/{itemId}', methods: ['DELETE'])]
    public function remove(int $itemId): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($this->getUser());
        if (!$this->cartService->removeItem($cart, $itemId)) {
            return $this->error('Cart item not found', 404);
        }

        return $this->success($cart->toArray());
    }

    #[Route('/clear', methods: ['DELETE'])]
    public function clear(): JsonResponse
    {
        $cart = $this->cartService->getOrCreateCart($this->getUser());
        $this->cartService->clearCart($cart);

        return $this->success($cart->toArray());
    }
}
