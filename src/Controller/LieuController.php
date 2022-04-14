<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'lieu')]
class LieuController extends AbstractController
{
    #[Route('/liste{id}', name: '_liste', requirements: ['id' => '\d+'])]
    public function listeLieuxByVille(): Response
    {

        return $this->render('lieu/index.html.twig', [
            'controller_name' => 'LieuController',
        ]);
    }
}
