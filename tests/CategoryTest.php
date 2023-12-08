<?php

namespace App\Tests;

use App\Entity\Category;
use App\Entity\Image;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    public function getCategory()
    {
        return (new Category())
            ->setName('First category')
            ->setDescription('First category description')
            ->setSlug('first-category');
    }

    public function testIfEquals(): void
    {
        $category = $this->getCategory();
        $this->assertEquals('First category', $category->getName());
        $this->assertEquals('First category description', $category->getDescription());
        $this->assertEquals('first-category', $category->getSlug());
    }

    public function testIfNotEquals(): void
    {
        $category = $this->getCategory();
        $this->assertNotEquals('Second category', $category->getName());
        $this->assertNotEquals('Second category desction', $category->getDescription());
        $this->assertNotEquals('first category', $category->getSlug());
    }

    public function testIfEmpty(): void
    {
        $category = $this->getCategory();
        $this->assertEmpty('', $category->getName());
        $this->assertEmpty('', $category->getDescription());
        $this->assertEmpty('', $category->getSlug());
    }
}
