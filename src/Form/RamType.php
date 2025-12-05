<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\Ram;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('marque')
            ->add('modele')
            ->add('prix')
            ->add('type')
            ->add('total')
            ->add('nbModule')
            ->add('Ascin')
            ->add('builds', EntityType::class, [
                'class' => Build::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ram::class,
        ]);
    }
}
