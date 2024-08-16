<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;


class BuildType extends AbstractType
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            $builder->add('prebuilt', CheckboxType::class, [
                'label' => 'Tag as prebuilt?',
                'required' => false,
            ]);
        }

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
