<?php

namespace App\EventSubscriber;

use App\Entity\Product;
use Doctrine\ORM\Events;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\EntityManagerInterface;

#[AsEntityListener(event: Events::prePersist, method: 'generateUniqueSlug', entity: Product::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'generateUniqueSlug', entity: Product::class)]
class ProductSlugSubscriber
{
    public function generateUniqueSlug(Product $product, LifecycleEventArgs $args): void
    {
        $em = $args->getObjectManager();
        if (!$product->getName()) {
            return;
        }
        $baseSlug = self::slugify($product->getName());
        $slug = $baseSlug;
        $i = 1;
        while ($this->slugExists($slug, $em, $product->getId())) {
            $slug = $baseSlug . '-' . $i;
            $i++;
        }
        $product->setSlug($slug);
    }

    private function slugExists(string $slug, EntityManagerInterface $em, ?int $currentId = null): bool
    {
        $qb = $em->getRepository(Product::class)->createQueryBuilder('p')
            ->select('p.id')
            ->where('p.slug = :slug')
            ->setParameter('slug', $slug);
        if ($currentId) {
            $qb->andWhere('p.id != :id')->setParameter('id', $currentId);
        }
        return (bool) $qb->getQuery()->getOneOrNullResult();
    }

    private static function slugify(string $text): string
    {
        $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);
        if (empty($text)) {
            return 'n-a';
        }
        return $text;
    }
}
