<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\AjouterSortieFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie', name: 'sortie_index')]
    public function index(): Response
    {
        return $this->render('sortie/index.html.twig');
    }

    #[Route('/sortie/ajouter', name: 'sortie_ajouter')]
    public function ajouter(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(AjouterSortieFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        return $this->render('sortie/ajouter.html.twig',[
            "ajouterSortieForm" => $form->createView()
        ]);
    }
}
