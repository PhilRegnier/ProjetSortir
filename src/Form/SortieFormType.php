<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('dateHeureDebut',
                DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'data' => (new \DateTime("now"))->modify('+5 days')
                ]
            )
            ->add('duree')
            ->add('dateLimiteInscription',
                DateType::class,
                [
                    'widget' => 'single_text',
                    'data' => (new \DateTime("now"))->modify('+4 days')
                ]
            )
            ->add('nbInscriptionsMax')
            ->add('infosSortie', TextareaType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
