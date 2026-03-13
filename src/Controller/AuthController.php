<?php

namespace App\Controller;

use App\Entity\User;
use App\DTO\RegisterRequest;
use App\Trait\ApiResponseTrait;
use App\Trait\ValidationTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api')]
class AuthController extends AbstractController
{
    use ApiResponseTrait;
    use ValidationTrait;

    #[Route('/register', methods: ['POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $hasher, ValidatorInterface $validator): JsonResponse
    {
        $dto = RegisterRequest::fromRequest(
            json_decode($request->getContent(), true) ?? []
        );

        $errors = $validator->validate($dto);
        if (count($errors) > 0) {
            return $this->formatValidationErrors($errors);
        }

        $user = new User();
        $user->setEmail($dto->email);
        $user->setName($dto->name);
        $user->setPassword($hasher->hashPassword($user, $dto->password));
        $user->setRoles(['ROLE_USER']);

        $em->persist($user);
        $em->flush();

        return $this->success(['message' => 'User registered!'], 201);
    }
}