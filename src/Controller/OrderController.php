<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{

    
    //  RETURNS ORDER CREATION FORM / EDIT FORM FOR AN EXISTING ORDER (unused)

    #[Route('/order/new', name: 'new_order')]
    #[Route('/order/{orderId}/edit', name: 'edit_order')]

    public function updateOrder(Order $order = null, Request $request, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        if (!$order){
            $order = new Order();
        }

        $form = $this->createForm(OrderType::class, $order);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $order->setRefId("69420");
            $order = $form->getData();
            $order->setUser($this->getUser());
            $order->setOrderDate(new \DateTime());
            $order->setTotal(0);

            $session = $request->getSession();
            $cart = $session->get('cart');
            if (!empty($cart)) {
                foreach ($cart as $productId => $item) {
                    $product = $productRepository->find($productId);
                    $orderItem = new OrderItem;
                    $orderItem->setProduct($product);
                    $orderItem->setQuantity($item['qtt']);
                    $entityManager->persist($orderItem);
                    $order->addOrderItem($orderItem);
                    $order->setTotal($order->getTotal() + $orderItem->getProduct()->getPrice() * $orderItem->getQuantity());
                }   
            }
            $entityManager->persist($order);
            $entityManager->flush();
            $session->set('cart', []);

            return $this->redirectToRoute('app_user');
        }

        return $this->render('order/new_order.html.twig', [
            'formOrder' => $form
        ]);
    }
}
