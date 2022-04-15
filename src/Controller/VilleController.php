<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManager;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        VilleRepository $villeRepository
    ): Response
    {
        $villes = $villeRepository->findAll();
        return $this->render('ville/gerer.html.twig', compact("villes"));
    }

    #[Route('/suprimer{id}', name: '_suprimer', requirements: ["id" => "\d+"])]
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

    #[Route('/suprimer{id}', name: '_suprimer', requirements: ["id" => "\d+"])]
    #[IsGranted("ROLE_ADMIN")]
    public function modifier(
        Ville $ville,
        VilleRepository $villeRepository
    ): Response
    {
        $villeRepository->remove($ville);
        $this->addFlash('success', 'La ville à été supprimé avec succès.');
        return $this->redirectToRoute('ville_gerer');
    }
}