<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\BuildComponent;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildComponentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('component', EntityType::class, [
                'class' => Product::class,
                'query_builder' => function (EntityRepository $entityRepository) {
                    return $entityRepository->createQueryBuilder('p')
                        ->where('p.category = :category')
                        ->setParameter('category', '1');
                },
                'choice_label' => 'label',
                'placeholder' => 'Choose a product',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BuildComponent::class,
        ]);
    }
}
