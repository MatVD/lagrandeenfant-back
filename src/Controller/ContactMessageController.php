<?php

namespace App\Controller;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class ContactMessageController extends AbstractController
{
    public function __invoke(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MailerInterface $mailer
    ): Response {
        $contact = $serializer->deserialize($request->getContent(), Contact::class, 'json');

        $errors = $validator->validate($contact);
        if (count($errors) > 0) {
            return new JsonResponse(['errors' => (string) $errors], Response::HTTP_BAD_REQUEST);
        }

        $entityManager->persist($contact);
        $entityManager->flush();

        // Envoi de l'email à l'administrateur
        $adminEmail = 'admin@example.com'; // À adapter
        $email = (new Email())
            ->from($contact->getEmail())
            ->to($adminEmail)
            ->subject('Nouveau message de contact')
            ->text(
                sprintf(
                    "Nom: %s\nEmail: %s\nMessage: %s",
                    $contact->getName(),
                    $contact->getEmail(),
                    $contact->getMessage()
                )
            );
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            // Log ou gestion d'erreur
        }

        return new JsonResponse(['success' => true], Response::HTTP_CREATED);
    }
}
