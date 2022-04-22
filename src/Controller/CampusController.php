<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusFormType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/campus', name: 'campus')]
class CampusController extends AbstractController
{
    #[Route('/gerer', name: '_gerer')]
    #[IsGranted("ROLE_ADMIN")]
    public function gerer(
        CampusRepository $campusRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $campuses = $campusRepository->findAll();

        //  Formulaire de recherche d'une ville
        $searchForm = $this->createFormBuilder()
            ->add("recherche", TextType::class,
                [
                    "label" => "Le nom contient :",
                    "required" => false
                ])
            ->getForm();
        $searchForm->handleRequest($request);
        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            if (!empty($searchForm->get("recherche")->getData())){
                $recherche = $searchForm->get("recherche")->getData();
                $campuses = $campusRepository->findWithFilter($recherche);
            }
        }

        $campus = new Campus();
        $campusForm = $this->createForm(CampusFormType::class);
        $campusForm->handleRequest($request);
        if ($campusForm->isSubmitted() && $campusForm->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();
            $this->addFlash('success', 'Le campus à été créée avec succès.');
            return $this->redirectToRoute("campus_gerer");
        }

        return $this->render('campus/gerer.html.twig',
            [
                "campusForm" => $campusForm->createView(),
                "searchForm" => $searchForm->createView(),
                "campuses" => $campuses
            ]);
    }

    #[Route('/supprimer{id}', name: '_supprimer', requirements: ["id" => "\d+"])]
    #[IsGranted("ROLE_ADMIN")]
    public function supprimer(
        Campus $campus,
        CampusRepository $campusRepository
    ): Response
    {
        $campusRepository->remove($campus);
        $this->addFlash('success', 'Le campus à été supprimé avec succès.');
        return $this->redirectToRoute('campus_gerer');
    }

    #[Route('/modifier{id}', name: '_modifier', requirements: ["id" => "\d+"])]
    #[IsGranted("ROLE_ADMIN")]
    public function modifier(
        Campus $campus,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {

        $campusForm = $this->createForm(CampusFormType::class, $campus);
        $campusForm->handleRequest($request);
        if ($campusForm->isSubmitted() && $campusForm->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le Campus à été modifié avec succès.');
            return $this->redirectToRoute("campus_gerer");
        }



        return $this->render('campus/modifier.html.twig',
            [
                "campusForm" => $campusForm->createView(),
                "campuses" => $campus
            ]);
    }
}