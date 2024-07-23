<?php

namespace EventListener;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddModelFieldSubscriber implements EventSubscriberInterface
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
        $this->addModelField($form, $this->category);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();
        $category = $data['category'] ?? $this->category;
        $this->addModelField($form, $category);
    }

    private function addModelField($form, $category)
    {
        $choices = [];

        switch ($category->getName()) {
            case 'Processor':
                $choices = [
                    'Intel Core i9' => 'i9',
                    'Intel Core i7' => 'i7',
                    'Intel Core i5' => 'i5',
                    'Intel Core i3' => 'i3',
                    'Intel Pentium' => 'pentium',
                    'Intel Celeron' => 'celeron',

                    'AMD Ryzen 9' => 'ryzen9',
                    'AMD Ryzen 7' => 'ryzen7',
                    'AMD Ryzen 5' => 'ryzen5',
                    'AMD Ryzen 3' => 'ryzen3',
                    'AMD Ryzen Threadripper' => 'threadripper',
                ];
                break;

            case 'Video card':
                $choices = [
                    'GeForce xx90 class' => 'xx90',
                    'GeForce xx80 class' => 'xx80',
                    'GeForce xx70 class' => 'xx70',
                    'GeForce xx60 class' => 'xx60',
                    'GeForce xx50 class' => 'xx50',
                    'Geforce GT' => 'gt',

                    'Radeon x900' => 'x900',
                    'Radeon x800' => 'x800',
                    'Radeon x700' => 'x700',
                    'Radeon x750' => 'x750',
                    'Radeon x600' => 'x600',
                    'Radeon x500' => 'x500',
                ];
                break;
            // Add more categories and choices as needed
        }

        $form->add('filterByModel', ChoiceType::class, [
            'label' => 'Filter by Model :',
            'choices' => $choices,
            'placeholder' => 'Choose a Model',
            'required' => false,
        ]);
    }
}