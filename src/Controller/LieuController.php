<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Form\LieuFormType;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'lieu')]
class LieuController extends AbstractController
{
    #[Route('/liste{id}', name: '_liste', requirements: ['id' => '\d+'])]
    public function listeParVille(): Response
    {
        return $this->render('lieu/index.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }
    #[Route('/ajouter', name: '_ajouter')]
    public function ajouter(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $lieu = new Lieu();
        $form = $this->createForm(LieuFormType::class, $lieu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();

            return $this->redirectToRoute('sortie_ajouter');

        }
        return $this->render('lieu/ajouter.html.twig',[
            "lieuForm" => $form->createView(),
            "lieu"  => $lieu
        ]);
    }
}
