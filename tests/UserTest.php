<?php

namespace App\Tests;

use App\Entity\BlogPost;
use App\Entity\Cart;
use App\Entity\Command;
use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use phpDocumentor\Reflection\PseudoTypes\True_;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function getUser()
    {
        return (new User())
            ->setFirstname('Mat')
            ->setLastname('Doe')
            ->setEmail('mat@example.com')
            ->setPassword('azerty123')
            ->setRoles(['ROLE_CUSTOMER'])
            ->setRegistrationDate(new \DateTime())
            ->setShippingInfos('23 rue du someil')
            ->addComment((new Comment())->setContent('Mon nouveau commentaire'))
            ->addCommand((new Command())->addProduct((new Product())->setName('Boucle 1')));
    }


    public function testIfEquals(): void
    {
        $user = $this->getUser();

        $this->assertSame('Mat', $user->getFirstname());
        $this->assertSame('Doe', $user->getLastname());
        $this->assertSame('mat@example.com', $user->getEmail());
        $this->assertSame('azerty123', $user->getPassword());
        $this->assertSame(['ROLE_CUSTOMER', 'ROLE_USER'], $user->getRoles());
        $this->assertInstanceOf(\DateTime::class, $user->getRegistrationDate());
        $this->assertSame('23 rue du someil', $user->getShippingInfos());
        $this->assertSame('Mon nouveau commentaire', $user->getComments()[0]->getContent());
        $this->assertSame('Boucle 1', $user->getCommands()[0]->getProducts()[0]->getName());
    }

    public function testIfNotEquals(): void
    {
        $user = $this->getUser();

        $this->assertNotEquals('Mut', $user->getFirstname());
        $this->assertNotEquals('Doer', $user->getLastname());
        $this->assertNotEquals('mat@exame.com', $user->getEmail());
        $this->assertNotEquals('azer123', $user->getPassword());
        $this->assertNotEquals(['ROLE_CUSTOMER'], $user->getRoles());
        $this->assertNotEquals('23 rue du sommeil', $user->getShippingInfos());
        $this->assertNotEquals('Mon dernier commentaire', $user->getComments()[0]->getContent());
        $this->assertNotEquals('Boucle 2', $user->getCommands()[0]->getProducts()[0]->getName());
    }

    public function testIfEmpty(): void
    {

        $user = $this->getUser();

        $this->assertEmpty('', $user->getFirstname());
        $this->assertEmpty('', $user->getLastname());
        $this->assertEmpty('', $user->getEmail());
        $this->assertEmpty('', $user->getPassword());
        $this->assertEmpty('', $user->getShippingInfos());
        $this->assertEmpty('', $user->getComments()[0]->getContent());
        $this->assertEmpty('', $user->getCommands()[0]->getProducts()[0]->getName());
    }
}
