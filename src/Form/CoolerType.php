<?php

namespace App\Form;

use App\Entity\Cooler;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CoolerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('isAio')
            ->add('heigth')
            ->add('tdp')
            ->add('marque')
            ->add('modele')
            ->add('prix')
            ->add('nbFan')
            ->add('socket')
            ->add('Ascin')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cooler::class,
        ]);
    }
}
