<?php

namespace App\Form;

use App\Entity\Build;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\BuildComponent;
use Doctrine\ORM\EntityRepository;
use App\Repository\ProductRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuildComponentType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $category = $options['category'] ?? null;
        $productsByCategory = $options['products_by_category'] ?? [];

        // Debugging output
        dump($category);
        dump($productsByCategory);

        $products = $category ? ($productsByCategory[$category->getId()] ?? []) : [];

        // Debugging output
        dump($products);

        $builder
            ->add('component', EntityType::class, [ 
                'class' => Product::class,
                'choices' => $products,
                'choice_label' => 'name',
                'placeholder' => 'Choose a product',
                'label' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BuildComponent::class,
            'category' => null,
            'products_by_category' => [],
        ]);

        $resolver->setAllowedTypes('category', ['null', 'App\Entity\Category']);
        $resolver->setAllowedTypes('products_by_category', 'array');
    }
}
