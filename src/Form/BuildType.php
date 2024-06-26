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
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class BuildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder          
        ->add('cpu', EntityType::class, [
            'class' => Product::class,
            'query_builder' => function (EntityRepository $entityRepository) {
                return $entityRepository->createQueryBuilder('p')
                    ->where('p.category = :category')
                    ->setParameter('category', 'cpu');
            },
            'choice_label' => 'label',
            'placeholder' => 'Processors',
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
