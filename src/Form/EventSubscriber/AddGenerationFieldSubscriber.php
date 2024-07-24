<?php

namespace EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddGenerationFieldSubscriber implements EventSubscriberInterface
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
        $this->addGenerationField($form, $this->category);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $category = $data['category'] ?? $this->category;
        $this->addGenerationField($form, $category);
    }

    private function addGenerationField($form, $category)
    {
        $choices = [];

        switch ($category->getName()) {
            case 'Processor':
                $choices = [
                    'Intel 14th gen' => 'intelGen14',
                    'Intel 13th gen' => 'intelGen13',
                    'Intel 12th gen' => 'intelGen12',
                    'Intel 11th gen' => 'intelGen11',
                    'Intel 10th gen' => 'intelGen10',

                    'AMD Ryzen 7000 series' => 'amdRyzen7000',
                    'AMD Ryzen 5000 series' => 'amdRyzen5000',
                    'AMD Ryzen 4000 series' => 'amdRyzen4000',
                    'AMD Ryzen 3000 series' => 'amdRyzen3000',
                    'AMD Ryzen 2000 series' => 'amdRyzen2000',
                ];
                break;

            case 'Video card':
                $choices = [
                    'GeForce RTX 4000' => 'rtx4000',
                    'GeForce RTX 3000' => 'rtx3000',
                    'GeForce RTX 2000' => 'rtx2000',

                    'Radeon RX 7000' => 'rx7000',
                    'Radeon RX 6000' => 'rx6000',
                    'Radeon RX 500' => 'rx500',

                    'Intel ARC' => 'intelArc',
                ];
                break;
            // Add more categories and choices as needed
        }

        $form->add('filterByGeneration', ChoiceType::class, [
            'label' => 'Generation',
            'choices' => $choices,
            'required' => false,
            'expanded' => true,
            'multiple' => true,
        ]);
    }
}