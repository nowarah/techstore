<?php

namespace App\Controller;

use App\DTO\CreateCategoryRequest;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Trait\ApiResponseTrait;
use App\Trait\ValidationTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/categories')]
class CategoryController extends AbstractController
{
    use ApiResponseTrait;
    use ValidationTrait;

    #[Route('', methods: ['GET'])]
    public function index(CategoryRepository $repository): JsonResponse
    {
        $categories = $repository->findAll();
        return $this->success(array_map(fn($c) => $c->toArray(), $categories));
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $dto = CreateCategoryRequest::fromRequest(
            json_decode($request->getContent(), true) ?? []
        );

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->formatValidationErrors($errors);
        }

        $category = new Category();
        $category->setName($dto->name);
        $category->setSlug($dto->slug);

        $em->persist($category);
        $em->flush();

       return $this->success($category->toArray(), 201);
       
    }

}