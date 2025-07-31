<?php

namespace App\Controller;

use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ImageUploadController extends AbstractController
{
    public function __invoke(Request $request, EntityManagerInterface $em, AuthorizationCheckerInterface $authChecker): JsonResponse
    {
        if (!$authChecker->isGranted('ROLE_ADMIN')) {
            return new JsonResponse(['message' => 'Accès refusé'], Response::HTTP_FORBIDDEN);
        }

        $file = $request->files->get('imageFile');
        if (!$file) {
            return new JsonResponse(['message' => 'Aucun fichier envoyé (clé imageFile attendue)'], Response::HTTP_BAD_REQUEST);
        }

        $image = new Image();
        $image->setImageFile($file);
        $em->persist($image);
        $em->flush();

        // Retourne l'IRI de la ressource créée
        return new JsonResponse([
            '@id' => '/api/images/' . $image->getId(),
            'id' => $image->getId(),
            'contentUrl' => $image->getContentUrlPublic(),
        ], Response::HTTP_CREATED);
    }
}
