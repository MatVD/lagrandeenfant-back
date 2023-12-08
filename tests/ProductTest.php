<?php

namespace App\Tests;

use App\Entity\Cart;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    public function getProduct()
    {
        return (new Product())
            ->setName('Bijoux1')
            ->setDescription('Un bijoux d\'une rare beauté')
            ->setDiscount('0.20')
            ->setPrice('20.00')
            ->setSlug('bijoux1')
            ->setQuantity(5)
            ->addComment((new Comment())->setAuthor((new User())->setFirstname('Mat')));
    }


    public function testIfEquals(): void
    {
        $product = $this->getProduct();

        $this->assertSame('Bijoux1', $product->getName());
        $this->assertSame('Un bijoux d\'une rare beauté', $product->getDescription());
        $this->assertSame('0.20', $product->getDiscount());
        $this->assertSame(20.0, $product->getPrice());
        $this->assertSame('bijoux1', $product->getSlug());
        $this->assertSame(5, $product->getQuantity());
        $this->assertSame('Mat', $product->getComments()[0]->getAuthor()->getFirstname());
    }

    public function testIfNotEquals(): void
    {
        $product = $this->getProduct();

        $this->assertNotSame('Bijou1', $product->getName());
        $this->assertNotSame('Un bix d\'une rare beauté', $product->getDescription());
        $this->assertNotSame(0.20, $product->getDiscount());
        $this->assertNotSame(20.01, $product->getPrice());
        $this->assertNotSame('bijou1', $product->getSlug());
        $this->assertNotSame(6, $product->getQuantity());
        $this->assertNotSame('Franck', $product->getComments()[0]->getAuthor()->getFirstname());
    }

    public function testIfEmpty(): void
    {

        $product = $this->getProduct();

        $this->assertEmpty('', $product->getName());
        $this->assertEmpty('', $product->getDescription());
        $this->assertEmpty('', $product->getDiscount());
        $this->assertEmpty('', $product->getPrice());
        $this->assertEmpty('', $product->getSlug());
        $this->assertEmpty('', $product->getQuantity());
        $this->assertEmpty('', $product->getComments()[0]->getAuthor()->getFirstname());
    }
}
