<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilFormType;
use App\Repository\ParticipantRepository;
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

    #[Route('/participant/profil{id}', name: 'participant_profil', requirements: ["id" => "\d+"])]
    public function monProfil(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator,
        EntityManagerInterface $entityManager,
        Participant $user
    ): Response
    {
        $user->setPseudo($this->getUser()->getPseudo());
        $user->setPrenom($this->getUser()->getPrenom());
        $user->setNom($this->getUser()->getNom());
        $user->setTelephone($this->getUser()->getTelephone());
        $user->setMail($this->getUser()->getMail());
        $user->setCampus($this->getUser()->getCampus());
        $form = $this->createForm(ProfilFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setMotPasse(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }
        return $this->render('participant/profil.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
