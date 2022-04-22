<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\LieuFormType;
use App\Form\SortieFormType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie')]
class SortieController extends AbstractController
{
    #[Route('/ajouter', name: '_ajouter')]
    #[IsGranted('ROLE_USER')]
    public function ajouter(
        Request $request,
        VilleRepository $villeRepository,
        EtatRepository $etatRepository,
        ParticipantRepository $participantRepository,
        LieuRepository $lieuRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieFormType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $organisateur = $participantRepository
                ->findOneBy(["mail" => $this->getUser()->getUserIdentifier()]);
            $sortie->setOrganisateur($organisateur);
            $sortie->setCampus($organisateur->getCampus());
            $sortie->setEtat($etatRepository->find(1));
            $sortie->setLieu($lieuRepository->findOneBy(["id" => $_POST['lieu_id']]));
            $entityManager->persist($sortie);
            $entityManager->flush();
            $this->addFlash('success', 'La sortie à été créée avec succès');
            return $this->redirectToRoute('main_connecte');
        }

        return $this->render('sortie/ajouter.html.twig',[
            "sortieForm" => $form->createView(),
            "villes"  => $villeRepository->findAll()
        ]);
    }

    #[Route('/detail/{id}', name: '_detail', requirements: ["id" => "\d+"])]
    public function detail(
        Sortie $sortie
    ): Response
    {
        return $this->render('sortie/detail.html.twig', compact("sortie"));
    }

    #[Route('/annuler/{id}', name: '_annuler', requirements: ["id" => "\d+"])]
    public function annuler(
        Sortie $sortie,
        Request $request,
        EntityManagerInterface $entityManager,
        EtatRepository $etatRepository
    ): Response
    {
        $motifForm = $this->createFormBuilder()
            ->add("motif", TextareaType::class)
            ->getForm();

        $motifForm->handleRequest($request);

        if ($motifForm->isSubmitted() && $motifForm->isValid()){
            $sortie->setMotif($motifForm->get("motif")->getData());

            # TODO : trouver qqch de plus beau pour fixer l'état
            $sortie->setEtat($etatRepository->find(6));

            $entityManager->flush();
            $this->addFlash('success', 'La sortie à été annulée avec succès.');
            return $this->redirectToRoute("main_connecte");
        }

        return $this->render('sortie/annuler.html.twig', [
            "motifForm" => $motifForm->createView(),
            "sortie" => $sortie
        ]);
    }

    #[Route('/modifier/{id}', name: '_modifier', requirements: ["id" => "\d+"])]
    public function modifier(
        Sortie $sortie,
        Request $request,
        VilleRepository $villeRepository,
        EtatRepository $etatRepository,
        EntityManagerInterface $entityManager
    ): Response

    {
        $modifSortieForm = $this->createForm(SortieFormType::class, $sortie);
        $modifSortieForm->handleRequest($request);
        if ($modifSortieForm->isSubmitted() && $modifSortieForm->isValid()){

            // TODO : contrôles métier éventuels (identique à ceux de la création d'une sortie

            $this->addFlash('success', 'La sortie a été modifiée.');

            if ($_POST['publier'] == "true") {
                $sortie->setEtat($etatRepository->find(2));
                $this->addFlash('success', 'La sortie a été publiée.');

            }
            $entityManager->flush();
        }

        $villes = $villeRepository->findAll();

        return $this->render('sortie/modifier.html.twig', [
                "modifSortieForm" => $modifSortieForm->createView(),
                "sortie" => $sortie,
                "villes"  => $villes
            ]
        );
    }

    #[Route('/publier/{id}', name: '_publier', requirements: ["id" => "\d+"] )]
    public function publier(
        Sortie $sortie,
        EtatRepository $etatRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortie->setEtat($etatRepository->find(2));
        $entityManager->flush();
        $this->addFlash('success', 'La sortie' . $sortie->getNom() . 'a été publiée.');
        return $this->redirectToRoute('main_connecte');
    }


    #[Route('/inscription/{id}', name: '_inscription', requirements: ["id" => "\d+"] )]
    public function inscription(
        Sortie $sortie,
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository
    )
    {
        $user = $participantRepository
            ->findOneBy(["mail" => $this->getUser()->getUserIdentifier()]);

        //On vérifie que la date de clôture n'est pas dépassée mais également que le statut de la sortie est bien "Ouverte"
        if (
            $sortie->getDateLimiteInscription()->format('Y-m-d') >= date("Y-m-d") &&
            $sortie->getEtat()->getId() == 2) {

            $sortie->addInscrit($user);
            $entityManager->flush();
            $this->addFlash('success', 'Vous êtes correctement inscrit de la sortie : ' . $sortie->getNom());

        } else {
            $this->addFlash('danger', "Vous ne pouvez pas vous inscrire de la sortie : " . $sortie->getNom());
        }
        return $this->redirectToRoute('main_connecte');
    }

    #[Route('/desinscription/{id}', name: '_desinscription', requirements: ["id" => "\d+"] )]
    public function desinscription(
        Sortie $sortie,
        EntityManagerInterface $entityManager,
        ParticipantRepository $participantRepository
    )

    {
        //Il est possible de se désinscrire seulement si la sortie n'a pas débuté
        if ($sortie->getDateHeureDebut()->format('Y-m-d') >= date("Y-m-d")) {
            $user = $participantRepository
                ->findOneBy(["mail" => $this->getUser()->getUserIdentifier()]);

            $sortie->removeInscrit($user);
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez été correctement désinscrit de la sortie : ' . $sortie->getNom());
        } else {
            $this->addFlash('danger', 'Vous ne pouvez pas vous désinscrire de la sortie : ' . $sortie->getNom());
        }


        return $this->redirectToRoute('main_connecte');

    }

    #[Route('/supprimer{id}', name: '_supprimer', requirements: ["id" => "\d+"])]
    public function supprimer(
        Sortie $sortie,
        SortieRepository $sortieRepository
    ): Response
    {
        $sortieRepository->remove($sortie);
        $this->addFlash('success', 'La sortie' . $sortie->getNom() . 'à été supprimé avec succès.');
        return $this->redirectToRoute('ville_gerer');
    }
}