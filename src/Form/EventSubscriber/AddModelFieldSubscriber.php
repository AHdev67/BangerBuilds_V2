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
                $label = "CPU model";
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
                $label = "GPU chipset";
                $choices = [

                    //NVIDIA
                    'GeForce RTX 4090' => '4090',
                    'GeForce RTX 4080' => '4080',
                    'GeForce RTX 4070' => '4070',
                    'GeForce RTX 4060' => '4060',

                    'GeForce RTX 3090' => '3090',
                    'GeForce RTX 3080' => '3080',
                    'GeForce RTX 3070' => '3070',
                    'GeForce RTX 3060' => '3060',
                    'GeForce RTX 3050' => '3050',
                    
                    'Geforce GT 1030' => 'gt1030',
                    'Geforce GT 730' => 'gt730',
                    'Geforce GT 710' => 'gt710',

                    //AMD
                    'Radeon RX 7900' => 'rx7900',
                    'Radeon RX 7800' => 'rx7800',
                    'Radeon RX 7700' => 'rx7700',
                    'Radeon RX 7600' => 'rx7600',

                    'Radeon RX 6800' => 'rx6800',
                    'Radeon RX 6700' => 'rx6750',
                    'Radeon RX 6600' => 'rx6600',
                    'Radeon RX 6500' => 'rx6500',
                    'Radeon RX 6400' => 'rx6400',

                    'Radeon RX 550' => 'rx550',

                    //INTEL
                    'Intel ARC A770' => 'a770',
                    'Intel ARC A750' => 'a750',
                    'Intel ARC A380' => 'a380',
                    'Intel ARC A310' => 'a310',
                ];
                break;
            // Add more categories and choices as needed
            default :
                $label = "label";
        }

        $form->add('filterByModel', ChoiceType::class, [
            'label' => $label,
            'choices' => $choices,
            'required' => false,
            'expanded' => true,
            'multiple' => true,
        ]);
    }
}