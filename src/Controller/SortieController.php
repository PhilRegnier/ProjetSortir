<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\LieuFormType;
use App\Form\SortieFormType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie')]
class SortieController extends AbstractController
{
    #[Route('/ajouter', name: '_ajouter')]
    public function ajouter(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {

        $sortie = new Sortie();
        $form = $this->createForm(SortieFormType::class);
        $form->handleRequest($request);
        $lieuForm = $this->createForm(LieuFormType::class);
        $lieuForm->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        return $this->render('sortie/ajouter.html.twig',[
            "sortieForm" => $form->createView(),
            "lieuForm"  => $lieuForm->createView()
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
            $etat = $etatRepository->findBy(["id" => 6]);
            $sortie->setEtat($etat);
            $entityManager->flush();
        }


        return $this->render('sortie/annuler.html.twig', [
            "motifForm" => $motifForm->createView(),
            "sortie" => $sortie
        ]);
    }
}