<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ComponentSlotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $category = $options['category'];

        $builder
            ->add('product', EntityType::class, [
                'class' => Product::class,
                'query_builder' => function (EntityRepository $entityRepository) use ($category) {
                    return $entityRepository->createQueryBuilder('p')
                        ->where('p.category = :category')
                        ->setParameter('category', $category);
                },
                'choice_label' => 'label',
                'placeholder' => 'Choose a product',
            ]);
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'category' => null,
        ]);
        $resolver->setAllowedTypes('category', [Category::class]);
    }
}
