<?php

namespace App\Controller;

use App\Repository\ImageRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PaymentController extends AbstractController
{
    private Session $session;

    public function __construct(
        private ProductRepository $productRepository,
        private RequestStack $requestStack,
        private ImageRepository $imageRepository,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $hasher,
        private UserRepository $userRepository
    ) {}


    #[Route('/api/checkout', name: 'api_checkout', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function checkout(Request $request): Response
    {
        header('Content-Type: application/json');

        $stripe = new StripeClient($this->getParameter('STRIPE_SECRET_KEY'));
        $frontUrl = $this->getParameter('REACT_APP_FRONT_URL');

        // Récupération des données du panier depuis le frontend
        $json = $request->getContent();
        $data = json_decode($json, true);

        if (!$data || !isset($data['items']) || empty($data['items'])) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Aucun élément dans le panier'
            ], 400);
        }

        // Construire les line_items pour Stripe à partir des données du panier
        $lineItems = [];
        foreach ($data['items'] as $item) {
            $imageUrl = null;
            if (!empty($item['image'])) {
                $img = is_array($item['image']) ? $item['image'][0] : $item['image'];
                // On construit l'URL absolue comme dans l'exemple fourni
                $imageUrl = $this->getParameter('BASE_URL') . $img;
            }
            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['name'],
                        'images' => $imageUrl ? [$imageUrl] : [],
                    ],
                    'unit_amount' => intval($item['price'] * 100), // Stripe utilise les centimes
                ],
                'quantity' => $item['quantity'],
            ];
        }

        // Options de livraison conditionnelles
        $shippingOptions = [];
        if (isset($data['shipping_cost']) && $data['shipping_cost'] > 0) {
            $shippingOptions[] = ['shipping_rate' => $this->getParameter('OTHER_SHIPPING')];
        } else {
            $shippingOptions[] = ['shipping_rate' => $this->getParameter('FREE_SHIPPING')];
        }

        // Récupération de l'utilisateur depuis la Bdd si connecté 
        $user = $this->getUser();
        $userIdentifier = $this->getUser() ? $this->getUser()->getUserIdentifier() : null;
        $userId = null;
        if ($user && method_exists($user, 'getId')) {
            $userEntity = $this->userRepository->findOneBy(['email' => $userIdentifier]);
            $userId = $userEntity ? $userEntity->getId() : null;
        }

        $sessionData = [
            'submit_type' => 'pay',
            'mode' => 'payment',
            'payment_method_types' => ['card'],
            'shipping_options' => $shippingOptions,
            'billing_address_collection' => "auto",
            'shipping_address_collection' => ['allowed_countries' => ['FR']],
            'allow_promotion_codes' => !empty($data['promo_code']),
            'line_items' => $lineItems,
            'success_url' => $frontUrl . "/success",
            'cancel_url' => $frontUrl . "/cart",
            'metadata' => [
                'user_id' => $userId,
                'promo_code' => $data['promo_code'] ?? '',
                'original_total' => $data['total'] ?? 0,
                'grand_total' => $data['grand_total'] ?? 0
            ]
        ];

        try {
            $this->session = $stripe->checkout->sessions->create($sessionData);

            return new JsonResponse([
                'success' => true,
                'message' => 'Session de paiement créée avec succès',
                'id' => $this->session->id,
                'url' => $this->session->url,
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Erreur lors de la création de la session: ' . $e->getMessage(),
            ], 500);
        }
    }
}
