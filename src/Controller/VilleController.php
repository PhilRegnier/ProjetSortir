<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Repository\LieuRepository;
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
    public function gerer(): Response
    {

        return $this->render('ville/gerer.html.twig');
    }
}
