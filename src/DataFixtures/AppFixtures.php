<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    public function load(ObjectManager $manager): void
    {

        // - Admin - //
        $user_admin = new User();
        $user_admin->setFirstname('John');
        $user_admin->setLastname('Dupond');
        $user_admin->setEmail('johndupont@gmail.com');
        // Criptage du mot de passe avant de le mettre en bdd
        $password = $this->hasher->hashPassword($user_admin, '5689yudGD788d');
        $user_admin->setPassword($password);
        $user_admin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user_admin);


        // - Client - //
        $user_customer = new User();
        $user_customer->setFirstname('Jean');
        $user_customer->setLastname('Dupré');
        $user_customer->setEmail('dupre@gmail.com');
        // Criptage du mot de passe avant de le mettre en bdd
        $password = $this->hasher->hashPassword($user_customer, 'vrfeGU45HTR4');
        $user_customer->setPassword($password);
        $user_customer->setRoles(["ROLE_USER"]);
        $manager->persist($user_customer);

        $manager->flush();

        // - BlogPosts - //
        for ($i = 1; $i < 10; $i++) {
            // Boucle pour générer 10 posts
            $post = (new BlogPost())
                ->setTitle("Titre du post$i")
                ->setContent("La description du post$i")
                ->setCreatedAt(new \DateTimeImmutable())
                ->setSlug("titre-post-$i");

            $manager->persist($post);
        }

        // - Comments - //
        for ($i = 1; $i < 5; $i++) {
            // Boucle pour générer 5 commentaires
            $commentaires = (new Comment())
                ->setAuthor($user_customer)
                ->setContent("Commentaire $i: Un très bon produit")
                ->setIsModerated(false)
                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($commentaires);
        }


        // - Categories - //
        for ($i = 1; $i < 5; $i++) {
            // Boucle pour générer 4 categories
            $categorie = (new Category())
                ->setName("Catégorie$i")
                ->setDescription("Catégorie $i: Un ensemble de produits")
                ->setSlug("Catégorie$i");

            $manager->persist($categorie);
        }

        $manager->flush();


        // // Images de bijoux avec un nom unique
        // $image1 = new Image();
        // $image1->setImageName('20230404_144808.webp');

        // $image2 = new Image();
        // $image2->setImageName('20230404_161250.webp');

        // $image3 = new Image();
        // $image3->setImageName('20230404_103557.webp');

        // $image4 = new Image();
        // $image4->setImageName('20230413_184601.webp');

        // $image5 = new Image();
        // $image5->setImageName('20221222_092117.jpg');

        // $image6 = new Image();
        // $image6->setImageName('20230314_140343.jpg');

        // $image7 = new Image();
        // $image7->setImageName('20230314_143151.jpg');

        // $image8 = new Image();
        // $image8->setImageName('20230314_144223.jpg');

        // $image10 = new Image();
        // $image10->setImageName('20230321_085945.jpg');

        // $image11 = new Image();
        // $image11->setImageName('20230408_151805.jpg');

        // $image12 = new Image();
        // $image12->setImageName('20230411_145700.jpg');

        // $image13 = new Image();
        // $image13->setImageName('boucles-violettes.webp');



        // // - Products - //
        // $product1 = (new Product())
        //     ->setName("Bijoux1")
        //     ->setDescription("Un bijoux d\'une rare beauté")
        //     ->addCategory($categorie)
        //     ->addImage($image1)
        //     ->addImage($image2)
        //     ->addImage($image3)
        //     ->setPrice("20.00")
        //     ->setSlug("bijoux1")
        //     ->setQuantity(5);

        // $manager->persist($product1);

        // $product2 = (new Product())
        //     ->setName("Bijoux2")
        //     ->setDescription("Un bijoux d\'une rare beauté")
        //     ->addCategory($categorie)
        //     ->addImage($image4)
        //     ->addImage($image5)
        //     ->addImage($image6)
        //     ->setPrice("20.00")
        //     ->setSlug("bijoux2")
        //     ->setQuantity(5);

        // $manager->persist($product2);

        // $product3 = (new Product())
        //     ->setName("Bijoux3")
        //     ->setDescription("Un bijoux d\'une rare beauté")
        //     ->addCategory($categorie)
        //     ->addImage($image7)
        //     ->addImage($image8)
        //     ->addImage($image10)
        //     ->setPrice("20.00")
        //     ->setSlug("bijoux3")
        //     ->setQuantity(5);

        // $manager->persist($product3);

        // $product4 = (new Product())
        //     ->setName("Bijoux4")
        //     ->setDescription("Un bijoux d\'une rare beauté")
        //     ->addCategory($categorie)
        //     ->addImage($image11)
        //     ->addImage($image12)
        //     ->addImage($image13)
        //     ->setPrice("20.00")
        //     ->setSlug("bijoux4")
        //     ->setQuantity(5);

        // $manager->persist($product4);


        for ($i = 0; $i < 10; $i++) {
            // Boucle pour générer 10 produits
            $product[$i] = (new Product())
                ->setName("Oeuvre$i")
                ->setDescription("Une oeuvre d\'une rare beauté")
                ->addCategory($categorie)
                ->setPrice("20.00")
                ->setSlug("oeuvre-$i")
                ->setQuantity(5);

            $manager->persist($product[$i]);
        }


        $manager->flush();
    }
}
