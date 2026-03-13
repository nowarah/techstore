<?php

namespace App\Controller;

use App\DTO\CreateProductRequest;
use App\Repository\ProductRepository;
use App\Service\ProductService;
use App\Trait\ApiResponseTrait;
use App\Trait\ValidationTrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/products')]
class ProductController extends AbstractController
{
    use ApiResponseTrait;
    use ValidationTrait;

    #[Route('', methods: ['GET'])]
    public function index(ProductRepository $repository): JsonResponse
    {
        $products = $repository->findAll();
        return $this->success(array_map(fn($p) => $p->toArray(), $products));
    }

    #[Route('/{slug}', methods: ['GET'])]
    public function show(string $slug, ProductRepository $repository): JsonResponse
    {
        $product = $repository->findOneBy(['slug' => $slug]);

        if (!$product) {
            return $this->error('Product not found', 404);
        }

        return $this->success($product->toArray());
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, ProductService $productService, ValidatorInterface $validator): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $dto = CreateProductRequest::fromRequest(
            json_decode($request->getContent(), true) ?? []
        );

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->formatValidationErrors($errors);
        }

        try {
            $product = $productService->createProduct($dto);
            return $this->success($product->toArray(), 201);
        } catch (\InvalidArgumentException $e) {
            return $this->error($e->getMessage(), 404);
        }
    }
}