<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use EventListener\AddBrandFieldSubscriber;
use EventListener\AddModelFieldSubscriber;
use EventListener\AddGenerationFieldSubscriber;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class FilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $category = $options['category'] ?? [];

        $builder
            ->add('orderBy', ChoiceType::class, [
                'label' => 'Order products by :',
                'choices' => [
                    'Score' => 'score_DESC',
                    'Highest price' => 'price_DESC',
                    'Lowest price' => 'price_ASC',
                    'Manufacturer' => 'brand_ASC',
                ],
            ])

            ->addEventSubscriber(new AddBrandFieldSubscriber($category))

            ->addEventSubscriber(new AddGenerationFieldSubscriber($category))

            ->addEventSubscriber(new AddModelFieldSubscriber($category))

            ->add('apply', SubmitType::class, [
                'label' => 'Apply filters',
                'attr' => ['class' => 'validateBtn btn'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'category' => [],
        ]);

        $resolver->setAllowedTypes('category', Category::class);
    }
}
