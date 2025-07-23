<?php

namespace App\Tests;

use App\Entity\Order;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
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


    public function getOrder()
    {
        $customer = (new User())->setFirstname('Mat');
        $product1 = $this->getProduct(1);
        $product2 = $this->getProduct(2);

        $totalPriceProduct1 = floatval($product1->getPrice()) * $product1->getQuantity();
        $totalPriceProduct2 = floatval($product2->getPrice()) * $product2->getQuantity();

        return (new Order())
            ->setCustomer($customer)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setShippingDate(new \DateTimeImmutable())
            ->setOrderStatus('pending')
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
        $Order = $this->getOrder();


        $this->assertSame($customer->getFirstname(), $Order->getCustomer()->getFirstName());
        $this->assertInstanceOf(\DateTimeImmutable::class, $Order->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $Order->getShippingDate());
        $this->assertEquals('pending', $Order->getOrderStatus());
        $this->assertEquals('ship01-2023-05-04', $Order->getShippingNumber());
        $this->assertEquals(5, $Order->getQuantityByProduct());
        $this->assertSame($product1->getName(), $Order->getProducts()[0]->getName());
        $this->assertSame($product2->getName(), $Order->getProducts()[1]->getName());
        $this->assertEquals(10, $Order->getTotalQuantity());
        // $this->assertEquals(200, $Order->getTotalPrice());
    }

    public function testIfNotEquals(): void
    {
        $Order = $this->getOrder();

        $this->assertNotEquals('Franck', $Order->getCustomer()->getFirstName());
        $this->assertNotEquals('2023', $Order->getCreatedAt());
        $this->assertNotEquals('2023', $Order->getShippingDate());
        $this->assertNotEquals('pendi', $Order->getOrderStatus());
        $this->assertNotEquals('shipp', $Order->getShippingNumber());
        $this->assertNotEquals(12, $Order->getQuantityByProduct());
        $this->assertNotEquals('Produit 12', $Order->getProducts()[0]->getName());
        $this->assertNotEquals('Produit 13', $Order->getProducts()[1]->getName());
        $this->assertNotEquals(122, $Order->getTotalQuantity());
    }

    public function testIfEmpty(): void
    {
        $Order = $this->getOrder();

        $this->assertEmpty('', $Order->getCustomer()->getFirstName());
        $this->assertEmpty('', $Order->getCreatedAt()->createFromFormat('Y-m-d H:i:s', ''));
        $this->assertEmpty('', $Order->getQuantityByProduct());
        $this->assertEmpty('', $Order->getProducts()[0]->getName());
        $this->assertEmpty('', $Order->getProducts()[1]->getName());
        $this->assertEmpty('', $Order->getTotalQuantity());
    }
}
