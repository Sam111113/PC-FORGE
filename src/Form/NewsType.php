<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\News;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NewsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('content', TextareaType::class, [
                'label' => 'Contenu',
                'attr' => ['rows' => 14], // juste pour le rendu initial
            ])
            ->add('accroche')
            ->add('image', ImageType::class, [
                'by_reference' => false,
                'required' => false,
                'data' => (new Image())->setContext('news'),
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => News::class,
        ]);
    }
}
