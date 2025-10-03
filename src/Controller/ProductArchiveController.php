<?php

namespace App\Controller;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/products', name: 'api_products_')]
class ProductArchiveController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {}

    #[Route('/{id}/archive', name: 'archive', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function archive(Product $product): JsonResponse
    {
        if ($product->isArchived()) {
            return $this->json([
                'error' => 'Le produit est déjà archivé (quantité = 0)'
            ], Response::HTTP_BAD_REQUEST);
        }

        $product->setQuantity(0);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Produit archivé avec succès (quantité mise à 0)',
            'product' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'quantity' => $product->getQuantity(),
                'isArchived' => $product->isArchived()
            ]
        ]);
    }

    #[Route('/{id}/unarchive', name: 'unarchive', methods: ['PATCH'])]
    #[IsGranted('ROLE_ADMIN')]
    public function unarchive(Product $product): JsonResponse
    {
        if (!$product->isArchived()) {
            return $this->json([
                'error' => 'Le produit n\'est pas archivé'
            ], Response::HTTP_BAD_REQUEST);
        }

        // On remet une quantité de 1 par défaut pour désarchiver
        $product->setQuantity(1);
        $this->entityManager->flush();

        return $this->json([
            'message' => 'Produit désarchivé avec succès (quantité mise à 1)',
            'product' => [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'quantity' => $product->getQuantity(),
                'isArchived' => $product->isArchived()
            ]
        ]);
    }
}
