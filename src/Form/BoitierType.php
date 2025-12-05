<?php

namespace App\Form;

use App\Entity\Boitier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BoitierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque')
            ->add('modele')
            ->add('prix')
            ->add('length')
            ->add('heigth')
            ->add('width')
            ->add('gpuMaxL')
            ->add('fanSlot')
            ->add('psuMaxL')
            ->add('aioSupport')
            ->add('mbFormFactor')
            ->add('FanSlotWidth')
            ->add('coolerMaxHeight')
            ->add('Ascin')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Boitier::class,
        ]);
    }
}
