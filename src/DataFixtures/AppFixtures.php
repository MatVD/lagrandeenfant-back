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
    ) {}

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


        // Création des catégories fixes
        $categories = [];
        $categoryData = [
            'Toiles personnalisées' => 'Toiles personnalisées',
            'Tableaux' => 'Tableaux',
            'Cartes et Affiches' => 'Cartes et Affiches',
            'Art digital' => 'Œuvres numériques'
        ];

        foreach ($categoryData as $name => $description) {
            $category = new Category();
            $category->setName($name)
                ->setDescription($description)
                ->setSlug(strtolower(str_replace(' ', '-', $name)));
            $categories[] = $category;
            $manager->persist($category);
        }

        $manager->flush();

        // Création des produits
        $productsData = [
            [
                'name' => 'Abstraction en bleu',
                'description' => 'Une œuvre abstraite aux tons bleus évoquant l\'océan et le ciel',
                'price' => 450.00,
                'quantity' => 1,
                'category' => $categories[0], // Peintures
                'discount' => '10%'
            ],
            [
                'name' => 'Nature morte contemporaine',
                'description' => 'Composition moderne de fruits et objets du quotidien',
                'price' => 680.00,
                'quantity' => 1,
                'category' => $categories[0], // Peintures
            ],
            [
                'name' => 'Envol féérique',
                'description' => 'Sculpture en acier représentant un oiseau en plein vol',
                'price' => 1000.00,
                'quantity' => 2,
                'category' => $categories[1], // Sculptures
                'discount' => '15%'
            ],
            [
                'name' => 'Envol métallique',
                'description' => 'Sculpture en acier représentant un oiseau en plein vol',
                'price' => 1200.00,
                'quantity' => 2,
                'category' => $categories[1], // Sculptures
                'discount' => '15%'
            ],
            [
                'name' => 'Lumière urbaine',
                'description' => 'Photographie nocturne de paysage urbain',
                'price' => 300.00,
                'quantity' => 5,
                'category' => $categories[2], // Photographies
            ],
            [
                'name' => 'Sophie stick',
                'description' => 'Photographie nocturne de paysage urbain',
                'price' => 300.00,
                'quantity' => 5,
                'category' => $categories[2], // Photographies
            ],
            [
                'name' => 'Pixels en mouvement',
                'description' => 'Création numérique dynamique et colorée',
                'price' => 250.00,
                'quantity' => 10,
                'category' => $categories[3], // Art digital
            ],
            [
                'name' => 'Or dur',
                'description' => 'Création numérique dynamique et colorée',
                'price' => 200.00,
                'quantity' => 10,
                'category' => $categories[3], // Art digital
            ]
        ];

        foreach ($productsData as $productData) {
            $product = new Product();
            $product->setName($productData['name'])
                ->setDescription($productData['description'])
                ->setPrice($productData['price'])
                ->setQuantity($productData['quantity'])
                ->setSlug(strtolower(str_replace(' ', '-', $productData['name'])))
                ->addCategory($productData['category']);

            if (isset($productData['discount'])) {
                $product->setDiscount($productData['discount']);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }
}
