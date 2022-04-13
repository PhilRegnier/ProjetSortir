<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main_home')]
    public function index(): Response
    {
        return $this->redirectToRoute('app_login');
    }

    #[Route('/main', name: 'main_connecte')]
    #[IsGranted ('ROLE_USER')]
    public function AccueilConnecte(
        ParticipantRepository $participantRepository,
        SortieRepository $sortieRepository
    ): Response
    {
        $participants = $participantRepository->findAll();
        $sorties = $sortieRepository->findAll();

        return $this->render('main/index.html.twig',compact("participants", "sorties"));
    }
}
