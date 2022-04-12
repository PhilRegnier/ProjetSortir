<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class ParticipantController extends AbstractController
{
    #[Route('/participant', name: 'app_participant')]
    public function index(): Response
    {
        return $this->render('participant/index.html.twig', [
            'controller_name' => 'ParticipantController',
        ]);
    }

    #[Route('/participant/profil', name: 'participant_profil')]
    public function monProfil(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator,
    ): Response
    {
        $participant = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $participant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $participant->setMotPasse(
                $userPasswordHasher->hashPassword(
                    $participant,
                    $form->get('plainPassword')->getData()
                )
            );
//            $entityManager->persist($participant);
//            $entityManager->flush();
            return $userAuthenticator->authenticateUser(
                $participant,
                $authenticator,
                $request
            );
        }
        return $this->render('participant/profil.html.twig');
    }
}
