<?php

namespace App\DataFixtures;

use App\Entity\BlogPost;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\User;
use App\Entity\Order;
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
        // Ajouter des images libres de droits aux produits
        $productsData = [
            [
                'name' => 'Abstraction en bleu',
                'description' => 'Une œuvre abstraite aux tons bleus évoquant l\'océan et le ciel',
                'price' => 450.00,
                'quantity' => 5,
                'category' => $categories[0], // Toiles personnalisées
                'discount' => '10%',
                'images' => [
                    'https://images.unsplash.com/photo-1547036967-23d11aacaee0?w=500',
                    'https://images.unsplash.com/photo-1578662996442-48f60103fc96?w=500'
                ]
            ],
            [
                'name' => 'Nature morte contemporaine',
                'description' => 'Composition moderne de fruits et objets du quotidien',
                'price' => 680.00,
                'quantity' => 3,
                'category' => $categories[0], // Toiles personnalisées
                'images' => [
                    'https://images.unsplash.com/photo-1578321272176-b7bbc0679853?w=500',
                    'https://images.unsplash.com/photo-1577720643271-6760b5ddc34a?w=500'
                ]
            ],
            [
                'name' => 'Envol féérique',
                'description' => 'Sculpture en acier représentant un oiseau en plein vol',
                'price' => 1000.00,
                'quantity' => 2,
                'category' => $categories[1], // Tableaux
                'discount' => '15%',
                'images' => [
                    'https://images.unsplash.com/photo-1578321272273-b7bbc0679853?w=500',
                    'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500'
                ]
            ],
            [
                'name' => 'Envol métallique',
                'description' => 'Sculpture en acier représentant un oiseau en plein vol',
                'price' => 1200.00,
                'quantity' => 2,
                'category' => $categories[1], // Tableaux
                'discount' => '15%',
                'images' => [
                    'https://images.unsplash.com/photo-1594736797933-d0401ba2fe65?w=500',
                    'https://images.unsplash.com/photo-1571115764595-644a1f56a55c?w=500'
                ]
            ],
            [
                'name' => 'Lumière urbaine',
                'description' => 'Photographie nocturne de paysage urbain',
                'price' => 300.00,
                'quantity' => 8,
                'category' => $categories[2], // Cartes et Affiches
                'images' => [
                    'https://images.unsplash.com/photo-1514565131-fce0801e5785?w=500',
                    'https://images.unsplash.com/photo-1519501025264-65ba15a82390?w=500'
                ]
            ],
            [
                'name' => 'Sophie stick',
                'description' => 'Photographie nocturne de paysage urbain',
                'price' => 300.00,
                'quantity' => 6,
                'category' => $categories[2], // Cartes et Affiches
                'images' => [
                    'https://images.unsplash.com/photo-1578662996442-48f12345678?w=500',
                    'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=500'
                ]
            ],
            [
                'name' => 'Pixels en mouvement',
                'description' => 'Création numérique dynamique et colorée',
                'price' => 250.00,
                'quantity' => 12,
                'category' => $categories[3], // Art digital
                'images' => [
                    'https://images.unsplash.com/photo-1618005182384-a83a8bd57fbe?w=500',
                    'https://images.unsplash.com/photo-1634017839464-5c339ebe3cb4?w=500'
                ]
            ],
            [
                'name' => 'Or dur',
                'description' => 'Création numérique dynamique et colorée',
                'price' => 200.00,
                'quantity' => 15,
                'category' => $categories[3], // Art digital
                'images' => [
                    'https://images.unsplash.com/photo-1634224556919-8ec2bfe93fac?w=500',
                    'https://images.unsplash.com/photo-1618556450994-a6a128ef0d9d?w=500'
                ]
            ]
        ];

        // Stockage des produits pour les commandes
        $products = [];

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

            // Ajout des images au produit
            if (isset($productData['images'])) {
                foreach ($productData['images'] as $imageUrl) {
                    $image = new Image();
                    $image->setImgUrl($imageUrl)
                        ->setProduct($product);
                    $manager->persist($image);
                }
            }

            $manager->persist($product);
            $products[] = $product; // Stockage pour les commandes

            // Génération de commentaires aléatoires pour chaque produit
            $commentsTexts = [
                "Magnifique œuvre, les couleurs sont sublimes !",
                "Très satisfait de cet achat, la qualité est au rendez-vous.",
                "Une pièce unique qui apporte beaucoup de caractère à mon salon.",
                "Excellent rapport qualité-prix, je recommande vivement.",
                "L'artiste a un vrai talent, cette création est exceptionnelle.",
                "Livraison rapide et emballage soigné, parfait !",
                "Exactement ce que je cherchais pour ma collection.",
                "Une œuvre qui ne laisse pas indifférent, très réussie.",
                "La technique utilisée est remarquable, bravo à l'artiste.",
                "Un investissement que je ne regrette absolument pas."
            ];

            // Génération d'un nombre aléatoire de commentaires (entre 1 et 4)
            $numberOfComments = rand(1, 4);
            $usedComments = array_rand($commentsTexts, $numberOfComments);

            // Si un seul commentaire, array_rand retourne un entier, pas un tableau
            if (!is_array($usedComments)) {
                $usedComments = [$usedComments];
            }

            foreach ($usedComments as $commentIndex) {
                $comment = new Comment();
                $comment->setAuthor($user_customer)
                    ->setContent($commentsTexts[$commentIndex])
                    ->setProduct($product)
                    ->setIsModerated(true)
                    ->setCreatedAt(new \DateTimeImmutable('-' . rand(1, 30) . ' days'));

                $manager->persist($comment);
            }
        }

        $manager->flush();

        // Génération de commandes
        $orderStatuses = ['En cours de traitement', 'Envoyée', 'Reçue'];
        $paymentStatuses = ['Non payée', 'Payée'];

        for ($i = 1; $i <= 15; $i++) {
            $order = new Order();
            $order->setCustomer($user_customer)
                ->setOrderStatus($orderStatuses[array_rand($orderStatuses)])
                ->setCreatedAt(new \DateTimeImmutable('-' . rand(1, 60) . ' days'))
                ->setPaymentStatus($paymentStatuses[array_rand($paymentStatuses)])
                ->setShippingDate(new \DateTimeImmutable('-' . rand(1, 30) . ' days'))
                ->setShippingNumber('SN' . rand(1000, 9999));

            // Sélection d'un nombre aléatoire de produits pour cette commande (1 à 3)
            $numberOfProducts = rand(1, 3);
            $selectedProductIndices = array_rand($products, $numberOfProducts);

            // Si un seul produit sélectionné, array_rand retourne un entier
            if (!is_array($selectedProductIndices)) {
                $selectedProductIndices = [$selectedProductIndices];
            }

            $totalQuantity = 0;
            $totalPrice = 0.0;

            // Associer les produits à la commande
            foreach ($selectedProductIndices as $productIndex) {
                $selectedProduct = $products[$productIndex];
                $quantityOrdered = rand(1, 2);

                // Cloner le produit pour cette commande spécifique
                $orderProduct = clone $selectedProduct;
                $orderProduct->setOrderProduct($order);

                $totalQuantity += $quantityOrdered;
                $totalPrice += $selectedProduct->getPrice() * $quantityOrdered;

                $manager->persist($orderProduct);
            }

            $order->setQuantityByProduct(rand(1, 5)) // Cette valeur semble être une propriété générique
                ->setTotalQuantity($totalQuantity)
                ->setTotalPrice($totalPrice);

            $manager->persist($order);
        }

        $manager->flush();
    }
}
