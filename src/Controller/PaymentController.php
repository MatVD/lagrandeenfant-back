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


    #[Route('/checkout', name: 'checkout', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function checkout(Request $request): Response
    {
        header('Content-Type: application/json');

        // Récupération de la var env.
        $stripe = new StripeClient($this->getParameter('STRIPE_SECRET_KEY'));

        $json = $request->getContent();

        $this->session = $stripe->checkout->sessions->create([
            'submit_type'                   => 'pay',
            'mode'                          => 'payment',
            'payment_method_types'          => ['card'],
            'shipping_options' => [
                ['shipping_rate' => $this->getParameter('FREE_SHIPPING')],
                ['shipping_rate' => $this->getParameter('OTHER_SHIPPING')]
            ],
            'billing_address_collection'    => "auto",
            'shipping_address_collection'   => ['allowed_countries' => ['FR']],
            'allow_promotion_codes' => true,
            'line_items' => [
                [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => "Un test",
                            'images' => ["https://cdn.pixabay.com/photo/2024/06/20/17/03/fishing-8842590_1280.jpg"],
                        ],
                        'unit_amount' => 1000,
                    ],
                    'quantity' => 1,
                ]
            ],
            'success_url' => "http://localhost:5173/paiement-reussi",
            'cancel_url' => "http://localhost:5173/paiement-echec",
        ]);

        if ($this->session->status === 'succeeded') {
            return new JsonResponse([
                'success' => true,
                'message' => 'Payment successful',
                'id' => $this->session->id,
                'url' => $this->session->url,
            ]);
        }
        return new JsonResponse([
            'success' => false,
            'message' => 'Payment failed',
            'id' => $this->session->id,
            'url' => $this->session->url,
        ]);
    }
}
