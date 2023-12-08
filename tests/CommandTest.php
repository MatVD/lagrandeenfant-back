<?php

namespace App\Tests;

use App\Entity\Command;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase
{
    public function getProduct($number)
    {
        return (new Product())
            ->setName('Bijoux' . $number)
            ->setDescription('Un bijoux d\'une rare beauté')
            ->setPrice(20.00)
            ->setSlug('bijoux' . $number)
            ->setQuantity(5)
            ->addComment(new Comment());
    }


    public function getCommand()
    {
        $customer = (new User())->setFirstname('Mat');
        $product1 = $this->getProduct(1);
        $product2 = $this->getProduct(2);

        $totalPriceProduct1 = floatval($product1->getPrice()) * $product1->getQuantity();
        $totalPriceProduct2 = floatval($product2->getPrice()) * $product2->getQuantity();

        return (new Command())
            ->setCustomer($customer)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setShippingDate(new \DateTimeImmutable())
            ->setCommandStatus('pending')
            ->setShippingNumber('ship01-2023-05-04')
            ->setdiscount(0.20)
            ->setQuantityByProduct(5)
            ->addProduct($product1)
            ->addProduct($product2)
            ->setTotalQuantity(
                $product1->getQuantity() +
                    $product2->getQuantity()
            )
            ->setTotalPrice(
                $totalPriceProduct1 +
                    $totalPriceProduct2
            );
    }


    public function testIfEquals(): void
    {
        $customer = (new User())->setFirstname('Mat');
        $product1 = $this->getProduct(1);
        $product2 = $this->getProduct(2);
        $command = $this->getCommand();


        $this->assertSame($customer->getFirstname(), $command->getCustomer()->getFirstName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $command->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $command->getShippingDate());
        $this->assertEquals('pending', $command->getCommandStatus());
        $this->assertEquals('ship01-2023-05-04', $command->getShippingNumber());
        $this->assertEquals(5, $command->getQuantityByProduct());
        $this->assertSame($product1->getName(), $command->getProducts()[0]->getName());
        $this->assertSame($product2->getName(), $command->getProducts()[1]->getName());
        $this->assertEquals(10, $command->getTotalQuantity());
        // $this->assertEquals(200, $command->getTotalPrice());
    }

    public function testIfNotEquals(): void
    {
        $command = $this->getCommand();

        $this->assertNotEquals('Franck', $command->getCustomer()->getFirstName());
        $this->assertNotEquals('2023', $command->getCreatedAt());
        $this->assertNotEquals('2023', $command->getShippingDate());
        $this->assertNotEquals('pendi', $command->getCommandStatus());
        $this->assertNotEquals('shipp', $command->getShippingNumber());
        $this->assertNotEquals(12, $command->getQuantityByProduct());
        $this->assertNotEquals('Produit 12', $command->getProducts()[0]->getName());
        $this->assertNotEquals('Produit 13', $command->getProducts()[1]->getName());
        $this->assertNotEquals(122, $command->getTotalQuantity());
    }

    public function testIfEmpty(): void
    {
        $command = $this->getCommand();

        $this->assertEmpty('', $command->getCustomer()->getFirstName());
        $this->assertEmpty('', $command->getCreatedAt()->createFromFormat('Y-m-d H:i:s', ''));
        $this->assertEmpty('', $command->getQuantityByProduct());
        $this->assertEmpty('', $command->getProducts()[0]->getName());
        $this->assertEmpty('', $command->getProducts()[1]->getName());
        $this->assertEmpty('', $command->getTotalQuantity());
    }
}
