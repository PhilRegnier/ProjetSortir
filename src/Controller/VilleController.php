<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleFormType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

#[Route('/ville', name: 'ville')]
class VilleController extends AbstractController
{
    #[Route('/obtenir/{id}', name: '_obtenir', requirements: ['id' => '\d+'])]
    public function updateSortie(
        Ville $ville,
        LieuRepository $lieuRepository
    ): array
    {
        /*
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

        $maxDepthHandler = function (
            $innerObject, $outerObject, string $attributeName, string $format = null, array $context = []) {
            return '/lieux/'.$innerObject->id;
        };

        $defaultContext = [
            AbstractObjectNormalizer::MAX_DEPTH_HANDLER => $maxDepthHandler,
        ];
        $normalizer = new ObjectNormalizer($classMetadataFactory, null, null, null, null, null, $defaultContext);

        $serializer = new Serializer([$normalizer]);

        */

        $encoder = new JsonEncoder();
        $normalizer = new ObjectNormalizer();
        $serializer = new Serializer([$normalizer], [$encoder]);

//        $lieux = $lieuRepository->findAll();
        $lieux = $lieuRepository->findOneBy(['id'=>1]);
        $lieux->setVille(null);
            /*["ville_id" => $ville->getId()],
            ["nom" => "ASC"]
        );*/

        return [
            $serializer->serialize($ville, 'json')
            //$serializer->normalize($lieux, 'json', [AbstractNormalizer::IGNORED_ATTRIBUTES => ['']])
        ];
    }

    #[Route('/gerer', name: '_gerer')]
    #[IsGranted("ROLE_ADMIN")]
    public function gerer(
        VilleRepository $villeRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $villes = $villeRepository->findAll();

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
                $villes = $villeRepository->findWithFilter($recherche);
            }
        }

        $ville = new Ville();
        $villeForm = $this->createForm(VilleFormType::class, $ville);
        $villeForm->handleRequest($request);
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();
            $this->addFlash('success', 'La ville à été créée avec succès.');
            return $this->redirectToRoute("ville_gerer");
        }

        return $this->render('ville/gerer.html.twig',
            [
                "villeForm" => $villeForm->createView(),
                "searchForm" => $searchForm->createView(),
                "villes" => $villes
            ]);
    }

    #[Route('/supprimer{id}', name: '_supprimer', requirements: ["id" => "\d+"])]
    #[IsGranted("ROLE_ADMIN")]
    public function supprimer(
        Ville $ville,
        VilleRepository $villeRepository
    ): Response
    {
        $villeRepository->remove($ville);
        $this->addFlash('success', 'La ville à été supprimé avec succès.');
        return $this->redirectToRoute('ville_gerer');
    }

    #[Route('/modifier{id}', name: '_modifier', requirements: ["id" => "\d+"])]
    #[IsGranted("ROLE_ADMIN")]
    public function modifier(
        Ville $ville,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {

        $villeForm = $this->createForm(VilleFormType::class, $ville);
        $villeForm->handleRequest($request);
        if ($villeForm->isSubmitted() && $villeForm->isValid()) {
            $entityManager->flush();
        $this->addFlash('success', 'La ville à été modifié avec succès.');
        return $this->redirectToRoute("ville_gerer");
        }



        return $this->render('ville/modifier.html.twig',
            [
                "villeForm" => $villeForm->createView(),
                "villes" => $ville
            ]);
    }
}