<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
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
        SortieRepository $sortieRepository,
        Request $request
    ): Response
    {
        //on prépare le formulaire des filtres qui sera envoyé au twig
        $filtreForm = $this->createFormBuilder()
            ->add('campus',
                EntityType::class,
                [
                    'class' => Campus::class,
                    'choice_label' => 'nom'
                ]
            )
            ->add('nomSortie',TextType::class,
                [
                    'label' => 'Le nom de la sortie contient',
                    'required' => false
                ] )
            ->add('dateSortieDebut',
                DateType::class, [
                    'widget' => 'single_text'
                ]
            )
            ->add('dateSortieFin',
                DateType::class, [
                    'widget' => 'single_text'
                ]
            )
            ->add('organisateur', CheckboxType::class)
            ->add('inscrit', CheckboxType::class)
            ->add('pasInscrit', CheckboxType::class)
            ->add('sortiesPassees', CheckboxType::class)

            ->getForm();

        $filtreForm->handleRequest($request);

        //Si des filtres ont été appliqués, on récupère les datas pour les exploiter dans findByWithFilter()
        if ($filtreForm->isSubmitted() && $filtreForm->isValid()
            && (!empty ($filtreForm->get('campus')->getData())
            || !empty ($filtreForm->get('nomSortie')->getData())
            || !empty ($filtreForm->get('dateSortieDebut')->getData())
            || !empty ($filtreForm->get('dateSortieFin')->getData())
            || !empty ($filtreForm->get('organisateur')->getData())
            || !empty ($filtreForm->get('inscrit')->getData())
            || !empty ($filtreForm->get('pasInscrit')->getData())
            || !empty ($filtreForm->get('sorties')->getData())))
        {
            $sortiesListe = $sortieRepository->findByWithFilter
            (
                $filtreForm->get('campus')->getData(),
                $filtreForm->get('nomSortie')->getData(),
                $filtreForm->get('dateSortieDebut')->getData(),
                $filtreForm->get('dateSortieFin')->getData(),
                $filtreForm->get('organisateur')->getData(),
                $filtreForm->get('inscrit')->getData(),
                $filtreForm->get('pasInscrit')->getData(),
                $filtreForm->get('sorties')->getData()
            );
        }
        //Si aucun filtre, on retourne simplement la liste de toutes les sorties
        else
        {
            $sortiesListe = $sortieRepository->findAll();
        }
        return $this->render('main/index.html.twig',
            [
                'filtreForm' => $filtreForm->createView(),
                'sortiesListe' => $sortiesListe
            ]
        );
    }
}
