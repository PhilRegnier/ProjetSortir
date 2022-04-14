<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Repository\ParticipantRepository;
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
        ParticipantRepository $participantRepository,
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

        $filtre = [];
        $filtreForm->handleRequest($request);

        if ($filtreForm->isSubmitted() && $filtreForm->isValid())
        {
            if (!empty ($filtreForm->get('campus')->getData())) {
                $filtre['campus'] = $filtreForm->get('campus')->getData();
            }
            if (!empty ($filtreForm->get('nomSortie')->getData())) {
                $filtre['nomSortie'] = $filtreForm->get('nomSortie')->getData();
            }
            if (!empty ($filtreForm->get('dateSortieDebut')->getData())) {
                $filtre['dateSortieDebut'] = $filtreForm->get('dateSortieDebut')->getData();
            }
            if (!empty ($filtreForm->get('dateSortieFin')->getData())) {
                $filtre['dateSortieFin'] = $filtreForm->get('dateSortieFin')->getData();
            }
            if (!empty ($filtreForm->get('organisateur')->getData())) {
                $filtre['organisateur'] = $filtreForm->get('organisateur')->getData();
            }
            if (!empty ($filtreForm->get('inscrit')->getData())) {
                $filtre['inscrit'] = $filtreForm->get('inscrit')->getData();
            }
            if (!empty ($filtreForm->get('pasInscrit')->getData())) {
                $filtre['pasInscrit'] = $filtreForm->get('pasInscrit')->getData();
            }
            if (!empty ($filtreForm->get('sortiesPassees')->getData())) {
                $filtre['etat'] = [6];
            }
        }
        else
        {
            $user = $participantRepository
                ->findOneBy(["mail" => $this->getUser()->getUserIdentifier()]);

            $filtre['campus'] = $user->getCampus();
            $filtre['etat'] = [1, 2, 3, 4, 5];
        }
        $sortiesListe = $sortieRepository->findByWithFilter($filtre);

        return $this->render('main/index.html.twig',
            [
                'filtreForm' => $filtreForm->createView(),
                'sortiesListe' => $sortiesListe
            ]
        );
    }
}
