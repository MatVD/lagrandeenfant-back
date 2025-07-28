<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class UserController extends AbstractController
{
    #[Route('/api/me', name: 'app_user')]
    public function index(SerializerInterface $serializer, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        // Si l'utilisateur n'est pas une instance de App\Entity\User, on le récupère par son email
        if (!$user instanceof User) {
            $currentUser = $entityManager->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()]);

            if (!$currentUser) {
                return new JsonResponse(['error' => 'User not found in database'], 404);
            }

            try {
                $currentUserInfo = [
                    'id' => $currentUser->getId(),
                    'email' => $currentUser->getEmail(),
                    'firstname' => method_exists($currentUser, 'getFirstName') ? $currentUser->getFirstName() : null,
                    'lastname' => method_exists($currentUser, 'getLastName') ? $currentUser->getLastName() : null,
                    'username' => method_exists($currentUser, 'getUsername') ? $currentUser->getUsername() : null,
                    'roles' => $currentUser->getRoles(),
                    'createdAt' => $currentUser->getCreatedAt() ? $currentUser->getCreatedAt()->format('Y-m-d H:i:s') : null,
                    'registrationDate' => $currentUser->getRegistrationDate() ? $currentUser->getRegistrationDate()->format('Y-m-d H:i:s') : null,
                    'isVerified' => method_exists($currentUser, 'isVerified') ? $currentUser->isVerified() : null,
                ];
            } catch (\Throwable $e) {
                return new JsonResponse(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
            }

            $data = $serializer->serialize($currentUserInfo, 'json');
            return new JsonResponse($data, 200, [], true);
        } else {
            // $user is an instance of User
            try {
                $userInfo = [
                    'id' => $user->getId(),
                    'email' => $user->getEmail(),
                    'firstname' => method_exists($user, 'getFirstName') ? $user->getFirstName() : null,
                    'lastname' => method_exists($user, 'getLastName') ? $user->getLastName() : null,
                    'roles' => $user->getRoles(),
                    'registrationDate' => $user->getRegistrationDate() ? $user->getRegistrationDate()->format('Y-m-d H:i:s') : null,
                    'isVerified' => method_exists($user, 'isVerified') ? $user->isVerified() : null,
                ];
            } catch (\Throwable $e) {
                return new JsonResponse(['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()], 500);
            }

            $data = $serializer->serialize($userInfo, 'json');
            return new JsonResponse($data, 200, [], true);
        }
    }
}
