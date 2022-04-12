<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\LieuFormType;
use App\Form\SortieFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie/ajouter', name: 'sortie_ajouter')]
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
}
