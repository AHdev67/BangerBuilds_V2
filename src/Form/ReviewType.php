<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Review;
use App\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => "Title goes here ...",
                ]
            ])

            ->add('content', TextareaType::class, [
                'label' => false,
                'attr' => [
                    'rows' => 10,
                    'placeholder' => "Content goes here ..."
                ]
            ])
            ->add('rating', ChoiceType::class, [
                'label' => "How do you feel about this product ?",
                'choices' => [
                    "😤 Very dissatisfied" => 1,
                    "🙁 Partially dissatisfied" => 2,
                    "😐 Mixed feelings" => 3,
                    "🙂 Mostly satisfied" => 4,
                    "😀 Very satisfied" => 5
                ], 
                'attr' => [
                ]
            ])

            ->add('Validate', SubmitType::class, [
                'attr' => [
                    'class' => 'validateBtn submitBtn btn',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
