<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieFormType;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
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

            # TODO : trouver qqch de plus beau pour fixer l'état
            $sortie->setEtat($etatRepository->find(1));

            $sortie->setLieu($lieuRepository->findOneBy(["id" => $_POST['lieu_id']]));
            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        $villes = $villeRepository->findAll();

        return $this->render('sortie/ajouter.html.twig',[
            "sortieForm" => $form->createView(),
            "villes"  => $villes
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
        }

        return $this->render('sortie/annuler.html.twig', [
            "motifForm" => $motifForm->createView(),
            "sortie" => $sortie
        ]);
    }

    #[Route('/modifier/{id}', name: '_modifier', requirements: ["id" => "\d+"])]
    public function modifier()
    {

    }
}