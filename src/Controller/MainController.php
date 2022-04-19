<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Repository\EtatRepository;
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
        EtatRepository $etatRepository,
        Request $request
    ): Response
    {
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
                DateType::class,
                [
                    'widget' => 'single_text',
                    'required' => false
                ]
            )
            ->add('dateSortieFin',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'required' => false
                ]
            )
            ->add('organisateur',
                CheckboxType::class,
                [
                    'required' => false
                ]
            )
            ->add('inscrit',
                CheckboxType::class,
                [
                    'required' => false
                ]
            )
            ->add('pasInscrit',
                CheckboxType::class,
                [
                    'required' => false
                ]
            )
            ->add('sortiesPassees',
                CheckboxType::class,
                [
                    'required' => false
                ]
            )
            ->getForm();

        $filtre = [];
        $filtreForm->handleRequest($request);
        $etatPasse = $etatRepository->findOneBy(['libelle'=> 'Passée']);

        dump('a'.$filtreForm->isSubmitted());
        if ($filtreForm->isSubmitted()) {
            dump('b'.$filtreForm->isValid());
        }

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
                $filtre['organisateurIdentifier'] = $this->getUser()->getUserIdentifier();
            }
            if (!empty ($filtreForm->get('inscrit')->getData())) {
                $filtre['inscrit'] = $this->getUser()->getUserIdentifier();
            }
            if (!empty ($filtreForm->get('pasInscrit')->getData())) {
                $filtre['pasInscrit'] = $this->getUser()->getUserIdentifier();
            }
            if (!empty ($filtreForm->get('sortiesPassees')->getData())) {
                $filtre['sortiesPassees'] = $etatPasse;
            }
        }
        else
        {
            $user = $participantRepository
                ->findOneBy(["mail" => $this->getUser()->getUserIdentifier()]);

            $filtre['campus'] = $user->getCampus();
            $filtre['sortiesNonPassees'] = $etatPasse;
        }
        $sortiesListe = $sortieRepository->findWithFilter($filtre);

        return $this->render('main/index.html.twig',
            [
                'filtreForm' => $filtreForm->createView(),
                'sortiesListe' => $sortiesListe
            ]
        );
    }
}
