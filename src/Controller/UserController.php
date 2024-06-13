<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[route('/user', name: 'app_user')]
    public function showUser(Request $request): Response
    {
        return $this->render('user/show_profile.html.twig', [

        ]);
    }

    #[Route('/user/cart', name: 'show_cart')]
    public function showCart(Request $request, ProductRepository $productRepository): Response
    {
        $total = 0;
        $session = $request->getSession();
        $cart = $session->get('cart');
        // if(isset($cart)) {
        //     foreach($cart as $item){
        //         $total = $total + $item['product']->getPrice() * $item['qtt'];
        //     }
        // }

        $products = [];

        if (isset($cart)) {
            foreach ($cart as $item) {
                $product = $productRepository->find($item);
                $products[] = $product;
                $total = $total + $product->getPrice() * $item['qtt'];
            }
        }

        return $this->render('user/show_cart.html.twig', [
            'products' => $products,
            'total' => $total
        ]);
    }

    #[Route('/{productId}/product/addToCart', name: 'add_item')]
    public function addProductToCart(#[MapEntity(id: 'productId')] Product $product, Request $request): Response
    {
        $session = $request->getSession();
        // Retrieve the cart from the session or initialize it as an empty array
        $cart = $session->get('cart', []);
        // Get the product ID
        $productId = $product->getId();
        // Check if the product is already in the cart
        if (isset($cart[$productId])) {
            // Increment the quantity if the product already exists
            $cart[$productId]['qtt'] += 1;
        } else {
            // Add the product to the cart with an initial quantity of 1
            $cart[$productId] = [
                'qtt' => 1,
            ];
        }
        dd($cart);
        // Save the updated cart back to the session
        $session->set('cart', $cart);

        $this->addFlash(
            'success', 
            'Item successfully added to your cart.'
        );
        return $this->redirectToRoute('show_cart');
    }

    #[Route('/user/cart/{itemId}/removeFromCart', name: 'remove_item')]
    public function removeItemFromCart(Request $request ): Response
    {
        $session = $request->getSession();
        $itemId = $request->attributes->get('itemId');
        $cart = $session->get('cart');
        unset($cart[$itemId]);
        $session->set('cart', $cart);

        return $this->redirectToRoute('show_cart');
    }

    #[Route('/user/cart/{itemId}/upqtt', name: 'up_quantity')]
    public function upQuantity(Request $request ): Response
    {
        $session = $request->getSession();
        $itemId = $request->attributes->get('itemId');
        $cart = $session->get('cart');
        $cart[$itemId]['qtt'] += 1;
        $session->set('cart', $cart);

        return $this->redirectToRoute('show_cart');
    }

    #[Route('/user/cart/{itemId}/downqtt', name: 'down_quantity')]
    public function downQuantity(Request $request ): Response
    {
        $session = $request->getSession();
        $itemId = $request->attributes->get('itemId');
        $cart = $session->get('cart');
        $cart[$itemId]['qtt'] -= 1;

        if ($cart[$itemId]['qtt'] > 0) {
            $session->set('cart', $cart);
            return $this->redirectToRoute('show_cart');
        }
        else {
            $this->addFlash(
                'warning', 
                'Item quantity must be equal or superior to 1.
                If you wish to remove the item from your cart, click the trashcan icon.'
            );
            return $this->redirectToRoute('show_cart');
        }
        
    }

    #[Route('/user/cart/flush', name: 'flush_cart')]
    public function flushCart(Request $request): Response
    {
        $session = $request->getSession();
        $session->set('cart', []);
        return $this->redirectToRoute('show_cart');
    }
}
