<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Build;
use App\Entity\Product;
use App\Entity\BuildComponent;
use App\Form\BuildComponentType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class BuildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $builder          
        ->add('buildComponents', CollectionType::class, [
            'entry_type' => BuildComponentType::class,
            'entry_options' => [
                'label' => false,
                // 'category' => 'category'
            ],
            'by_reference' => false,
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
        ])

        ->add('save', SubmitType::class, [
            'label' => 'Save Build',
            'attr' => ['class' => 'validateBtn submitBtn btn'],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Build::class,
        ]);
    }
}
