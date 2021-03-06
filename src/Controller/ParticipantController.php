<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ParticipantFormType;
use App\Repository\ParticipantRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/participant', name: 'participant')]
class ParticipantController extends AbstractController
{
    #[Route('/monprofil', name: '_monprofil')]
    #[IsGranted('ROLE_USER')]
    public function monProfil(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        UserAuthenticatorInterface $userAuthenticator,
        AppAuthenticator $authenticator,
        EntityManagerInterface $entityManager
    ): Response
    {
        $user = $this->getUser();
        $user->setPseudo($this->getUser()->getPseudo());
        $user->setPrenom($this->getUser()->getPrenom());
        $user->setNom($this->getUser()->getNom());
        $user->setTelephone($this->getUser()->getTelephone());
        $user->setMail($this->getUser()->getMail());
        $user->setCampus($this->getUser()->getCampus());

        $form = $this->createForm(ParticipantFormType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('plainPassword')->getData() === $form->get('confirmation')->getData()) {
                $user->setMotPasse(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $entityManager->flush();

                $this->addFlash('success', 'Votre profil a ??t?? modifi?? avec succ??s.');

                return $userAuthenticator->authenticateUser(
                    $user,
                    $authenticator,
                    $request
                );
            }
            else {
                $this->addFlash('danger', 'Les mots de passe saisis sont diff??rents.');
            }
        }
        return $this->render('participant/profil.html.twig', [
            'participantForm' => $form->createView(),
        ]);
    }

    #[Route('/detail{id}', name: '_detail', requirements: ["id" => "\d+"])]
    #[IsGranted('ROLE_USER')]
    public function detail(
        Participant $participant
    ):Response
    {
        return $this->render('participant/detail.html.twig', compact("participant"));
    }
}
