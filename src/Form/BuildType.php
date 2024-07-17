<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Build;
use App\Entity\Product;
use App\Entity\BuildComponent;
use App\Form\BuildComponentType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class BuildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
//-------------------------------------------------------------------------------------------------------------------------------------      
        // $productsByCategory = $options['products_by_category'] ?? [];

        // $builder 
        //     ->add('buildComponents', CollectionType::class, [
        //     'entry_type' => BuildComponentType::class,
        //     'entry_options' => function($categoryKey, $productsByCategory) use ($productsByCategory) {
        //             return [
        //                 'label' => false,
        //                 'category' => $categoryKey,
        //                 'products_by_category' => $productsByCategory,
        //             ];
        //         },
        //     'by_reference' => false,
        //     'allow_add' => true,
        //     'allow_delete' => true,
        //     'prototype' => true,
        // ])
//-------------------------------------------------------------------------------------------------------------------------------------
// # STATIC UNMAPPED FIELDS WITH MANUAL DATA FETCH

    $builder 
        ->add('cpu', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                ->where('p.category = :category')
                ->setParameter('category', 1)
                ;
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('cpuCooler', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 2);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('motherboard', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 3);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('memory', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 4);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('graphicsCard', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 5);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('storage', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 6);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('powerSupply', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 9);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('case', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 8);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('opticalDrive', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'required' =>false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 10);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('wiredNetworkCard', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'required' =>false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 11);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('wirelessNetworkCard', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'required' =>false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 12);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('soundCard', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'required' =>false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 13);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('operatingSystem', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'required' =>false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 14);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('caseFan', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'required' =>false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 16);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])

        ->add('service', EntityType::class, [
            'class' => Product::class,
            'mapped' => false,
            'required' =>false,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 26);
            },
            'choice_label' => 'label',
            'placeholder' => 'Choose a product',
        ])
//-------------------------------------------------------------------------------------------------------------------------------------
        ->add('name', TextType::class, [
            'label' => 'Name of the build',
        ])
        ->add('prebuilt', CheckboxType::class, [
            'label' => 'Tag as prebuilt ?',
        ])

        ->add('save', SubmitType::class, [
            'label' => 'Save build',
            'attr' => ['class' => 'validateBtn btn'],
        ]);
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Build::class,
            'products_by_category' => [],
        ]);

        $resolver->setAllowedTypes('products_by_category', 'array');
    }
}
