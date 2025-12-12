<?php

namespace App\Form;

use App\Entity\Boitier;
use App\Entity\Build;
use App\Entity\Cooler;
use App\Entity\Cpu;
use App\Entity\Fan;
use App\Entity\Gpu;
use App\Entity\Image;
use App\Entity\Motherboard;
use App\Entity\Psu;
use App\Entity\Ram;
use App\Entity\Storage;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('description')
            ->add('isPreBuild')
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('isMonthBuild')
            // ->add('totalPrice')
            ->add('name')
            ->add('Motherboard', EntityType::class, [
                'class' => Motherboard::class,
                'choice_label' => function (Motherboard $motherboard) {
                    return $motherboard->getMarque() . ' ' . $motherboard->getModele();
                },
            ])
            ->add('Cpu', EntityType::class, [
                'class' => Cpu::class,
                'choice_label' => function (Cpu $cpu) {
                    return $cpu->getMarque() . ' ' . $cpu->getModele();
                },
            ])
            ->add('Gpu', EntityType::class, [
                'class' => Gpu::class,
                'choice_label' => function (Gpu $gpu) {
                    return $gpu->getMarque() . ' ' . $gpu->getModele();
                },
                'multiple' => true,
            ])
            ->add('Ram', EntityType::class, [
                'class' => Ram::class,
                'choice_label' => function (Ram $ram) {
                    return $ram->getMarque() . ' ' . $ram->getModele();
                },
                'multiple' => true,
            ])
            ->add('Boitier', EntityType::class, [
                'class' => Boitier::class,
                'choice_label' => function (Boitier $boitier) {
                    return $boitier->getMarque() . ' ' . $boitier->getModele();
                },
            ])
            ->add('Psu', EntityType::class, [
                'class' => Psu::class,
                'choice_label' => function (Psu $psu) {
                    return $psu->getMarque() . ' ' . $psu->getModele();
                },
            ])
            ->add('Cooler', EntityType::class, [
                'class' => Cooler::class,
                'choice_label' => function (Cooler $cooler) {
                    return $cooler->getMarque() . ' ' . $cooler->getModele();
                },
            ])
            ->add('Storage', EntityType::class, [
                'class' => Storage::class,
                'choice_label' => function (Storage $storage) {
                    return $storage->getMarque() . ' ' . $storage->getModele();
                },
                'multiple' => true,
            ])
            ->add('Fan', EntityType::class, [
                'class' => Fan::class,
                'choice_label' => function (Fan $fan) {
                    return $fan->getMarque() . ' ' . $fan->getModele();
                },
                'multiple' => true,
            ])
            ->add('image', ImageType::class, [
                'by_reference' => false,
                'required' => false,
            ])
            ->add('User', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('Name')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Build::class,
        ]);
    }
}
