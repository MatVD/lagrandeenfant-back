<?php

namespace App\Tests;

use App\Entity\BlogPost;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function getComment()
    {
        $customer = (new User())->setFirstname('Mat');
        $admin = (new User())->setFirstname('Milodie');
        $product1 = (new Product())->setName('produit1');
        $blogPost = (new BlogPost())->setTitle('Blog post 1');

        return (new Comment())
            ->setAuthor($customer)
            ->setContent('Un très bon produit')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setIsModerated(true)
            ->setProduct($product1)
            ->setBlogPost($blogPost);
    }


    public function testIfEquals(): void
    {
        $customer = (new User())->setFirstname('Mat');
        $admin = (new User())->setFirstname('Milodie');
        $product1 = (new Product())->setName('produit1');
        $blogPost = (new BlogPost())->setTitle('Blog post 1');
        $comment = $this->getComment();

        $this->assertSame($customer->getFirstName(), $comment->getAuthor()->getFirstName());
        $this->assertSame('Un très bon produit', $comment->getContent());
        $this->assertInstanceOf(\DateTimeImmutable::class, $comment->getCreatedAt());
        $this->assertEquals(true, $comment->isIsModerated());
        $this->assertSame($product1->getName(), $comment->getProduct()->getName());
        $this->assertEquals($blogPost->getTitle(), $comment->getBlogPost()->getTitle());
    }

    public function testIfNotEquals(): void
    {
        $customer = (new User())->setFirstname('Mat');
        $admin = (new User())->setFirstname('Milodie');
        $product1 = (new Product())->setName('produit1');
        $blogPost = (new BlogPost())->setTitle('Blog post 1');
        $comment = $this->getComment();

        $this->assertNotSame($customer, $comment->getAuthor()->getFirstName());
        $this->assertNotSame('Un très mauvais produit', $comment->getContent());
        $this->assertNotSame(\DateTime::class, $comment->getCreatedAt());
        $this->assertNotSame(false, $comment->isIsModerated());
        $this->assertNotSame($product1, $comment->getProduct()->getName());
        $this->assertNotSame($blogPost, $comment->getBlogPost()->getTitle());
    }

    public function testIfEmpty(): void
    {
        $comment = $this->getComment();

        $this->assertEmpty('', $comment->getAuthor());
        $this->assertEmpty('', $comment->getCreatedAt()->createFromFormat('Y-m-d H:i:s', ''));
        $this->assertEmpty('', $comment->getContent());
        $this->assertEmpty('', $comment->getProduct()->getName());
        $this->assertEmpty('', $comment->getBlogPost()->getTitle());
    }
}
