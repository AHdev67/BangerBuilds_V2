<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/order/new', name: 'new_order')]
    #[Route('/order/{orderId}/edit', name: 'edit_order')]

    public function newOrder(Order $order = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$order){
            $order = new Order();
        }

        $form = $this->createForm(OrderType::class, $order);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $order->setRefId("69420");
            $order = $form->getData();
            $order->setOrderDate(new \DateTime());
            $order->setTotal(0);

            $session = $request->getSession();
            $cart = $session->get('cart');
            if ($cart) {
                foreach ($cart as $item) {
                    $product = $item['product'];
                    $entityManager->persist($product);
                    dd($product);
                    $order->addProduct($product);
                    $order->setTotal($order->getTotal() + $product->getPrice());
                }   
            }

            $entityManager->persist($order);
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }

        return $this->render('order/new_order.html.twig', [
            'formOrder' => $form
        ]);
    }
}
