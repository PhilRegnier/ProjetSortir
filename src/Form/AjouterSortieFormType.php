<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AjouterSortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut')
            ->add('dateLimiteInscription')
            ->add('nbInscriptionsMax')
            ->add('duree')
            ->add('infosSortie')
            ->add('campus',
            EntityType::class,
            [
                "class" => Campus::class,
                "choice_label" => "nom"
            ])
            ->add('ville',
            EntityType::class,
                [
                    "class" => Ville::class,
                    "choice_label" => "nom"
                ])
            ->add("lieu",
            EntityType::class,
            [
                "class" => Lieu::class,
                "choice_label" => "nom"
            ])
            ->add("rue",
                EntityType::class,
                [
                    "class" => Lieu::class,
                    "choice_label" => "rue"
                ])
            ->add('codePostal',
                EntityType::class,
                [
                    "class" => Ville::class,
                    "choice_label" => "codePostal"
                ])
            ->add("latitude",
                EntityType::class,
                [
                    "class" => Lieu::class,
                    "choice_label" => "latitude"
                ])
            ->add("longitude",
                EntityType::class,
                [
                    "class" => Lieu::class,
                    "choice_label" => "longitude"
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
