<?php

namespace App\Tests;

use App\Entity\BlogPost;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class BlogPostTest extends TestCase
{
    public function getEntity()
    {
        $user = (new User())
            ->setFirstname('Milodie');

        return (new BlogPost())
            ->setTitle('Mon tout premier bijoux')
            ->setContent('Un bijoux unique')
            ->setCreatedAt(new \DateTimeImmutable())
            ->setSlug('tout-premier-bijou');
    }


    public function testIfEquals()
    {
        $entity = $this->getEntity();
        $this->assertEquals('Mon tout premier bijoux', $entity->getTitle());
        $this->assertEquals('Un bijoux unique', $entity->getContent());
        $this->assertEquals('tout-premier-bijou', $entity->getSlug());
    }


    public function testIfNotEquals()
    {
        $entity = $this->getEntity();
        $this->assertNotEquals('Mon tout premier', $entity->getTitle());
        $this->assertNotEquals('Un bijoux unie', $entity->getContent());
        $this->assertNotEquals('image1.png', $entity->getImages());
        $this->assertNotEquals(' ', $entity->getSlug());
    }

    public function testIfEmpty()
    {
        $emptyEntity = new BlogPost();
        $this->assertEquals('', $emptyEntity->getTitle());
        $this->assertEquals('', $emptyEntity->getContent());
        $this->assertEquals([], $emptyEntity->getImages()->toArray());
        $this->assertEquals('', $emptyEntity->getSlug());
    }
}
