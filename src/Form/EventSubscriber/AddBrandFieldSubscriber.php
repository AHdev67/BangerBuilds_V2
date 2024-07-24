<?php

namespace EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddBrandFieldSubscriber implements EventSubscriberInterface
{
    private $category;

    public function __construct($category)
    {
        $this->category = $category;
    }

    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $this->addBrandField($form, $this->category);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $category = $data['category'] ?? $this->category;
        $this->addBrandField($form, $category);
    }

    private function addBrandField($form, $category)
    {
        $choices = [];

        switch ($category->getName()) {
            case 'Processor':
                $choices = [
                    'Intel' => 'intel',
                    'Amd' => 'amd',
                ];
                break;

            case 'Video card':
                $choices = [
                    'Nvidia' => 'Nvidia',
                    'AMD' => 'AMD',
                    'Intel' => 'Intel',
                ];
                break;
            // Add more categories and choices as needed
        }

        $form->add('filterByBrand', ChoiceType::class, [
            'label' => 'Manufacturer :',
            'choices' => $choices,
            'required' => false,
            'expanded' => true,
            'multiple' => true,
        ]);
    }
}