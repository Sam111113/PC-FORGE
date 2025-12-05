<?php

namespace App\Form;

use App\Entity\Motherboard;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MotherboardType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque')
            ->add('modele')
            ->add('prix')
            ->add('socket')
            ->add('pcieSlot')
            ->add('pcieModule')
            ->add('slotM2')
            ->add('sataPort')
            ->add('memoryMax')
            ->add('memoryType')
            ->add('memorySlot')
            ->add('formFactor')
            ->add('Ascin')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Motherboard::class,
        ]);
    }
}
