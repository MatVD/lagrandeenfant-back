<?php

namespace App\ApiPlatform\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\Entity\Product;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class ProductArchiveExtension implements QueryCollectionExtensionInterface
{
    public function __construct(
        private Security $security
    ) {}

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        Operation $operation = null,
        array $context = []
    ): void {
        if ($resourceClass !== Product::class) {
            return;
        }

        // Vérifier si c'est la route pour les produits archivés via le contexte
        $requestUri = $context['request_uri'] ?? '';
        if (str_contains($requestUri, '/archived')) {
            $this->addQuantityFilter($queryBuilder, 0);
            return;
        }

        // Pour les autres routes, exclure les produits avec quantité = 0 par défaut
        // sauf si l'utilisateur utilise explicitement le filtre
        $showArchived = $context['filters']['archived'] ?? null;

        if ($showArchived === null || $showArchived === false || $showArchived === 'false') {
            $this->addQuantityFilter($queryBuilder, '>0');
        }
    }

    private function addQuantityFilter(QueryBuilder $queryBuilder, string|int $condition): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[0];

        if ($condition === 0) {
            $queryBuilder->andWhere(sprintf('%s.quantity = 0', $rootAlias));
        } else {
            $queryBuilder->andWhere(sprintf('%s.quantity > 0', $rootAlias));
        }
    }
}
