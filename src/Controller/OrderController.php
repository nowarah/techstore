<?php

namespace App\Controller;

use App\Service\OrderService;
use App\Trait\ApiResponseTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\NumberFormatter;

#[Route('/api/orders')]
class OrderController extends AbstractController
{
    use ApiResponseTrait;

    #[Route('/checkout', methods: ['POST'])]
    public function checkout(OrderService $orderService): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        try {
            $order = $orderService->createOrderFromCart($this->getUser());
            return $this->success([
                'id' => $order->getId(),
                'total' => $order->getTotal(),
                'totalFormatted' => NumberFormatter::format($order->getTotal()),
                'status' => $order->getStatus()->value,
            ], 201);
        } catch (\RuntimeException $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}