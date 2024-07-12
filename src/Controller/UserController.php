<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Build;
use App\Entity\Product;
use App\Repository\BuildRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{


    //  RETURNS THE USER PROFILE PAGE FOR CURRENT USER

    #[route('/user', name: 'app_user')]
    public function showUser(Request $request): Response
    {
        $user = $this->getUser();
        $orders = $user->getOrders();
        $builds = $user->getBuilds();
        return $this->render('user/show_profile.html.twig', [
            'orders' => $orders,
            'builds' => $builds
        ]);
    }

    //  RETURNS THE SHOPPING CART OF CURRENT USER

    #[Route('/user/cart', name: 'show_cart')]
    public function showCart(Request $request, ProductRepository $productRepository, BuildRepository $buildRepository): Response
    {
        $total = 0;
        $session = $request->getSession();
        $cart = $session->get('cart');

        $items = [];

        if (!empty($cart)) {
            foreach ($cart as $itemId => $item) {
                if($item['type'] == 'product'){
                    $product = $productRepository->find($itemId);
                    if ($product) {
                        $items[] = ['product' => $product, 'qtt' => $item['qtt'], 'type' => $item['type']];
                        $total += $product->getPrice() * $item['qtt'];
                    }
                }
                elseif($item['type'] == 'build'){
                    $build = $buildRepository->find($itemId);
                    if ($build) {
                        $items[] = ['build' => $build, 'qtt' => $item['qtt'], 'type' => $item['type']];
                        $total += $build->getTotal() * $item['qtt'];
                    }
                }
            }
        }

        return $this->render('user/show_cart.html.twig', [
            'items' => $items,
            'total' => $total
        ]);
    }


    //  ADDS A NEW PRODUCT TO THE CART / ADDS 1 TO ITEM QUANTITY IF PRODUCT IS ALREADY IN THE CART

    #[Route('/{productId}/product/addToCart', name: 'addProduct_ToCart')]
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
                'type' => 'product'
            ];
        }
        // Save the updated cart back to the session
        $session->set('cart', $cart);

        $this->addFlash(
            'success', 
            'Item successfully added to your cart.'
        );
        return $this->redirectToRoute('show_cart');
    }

    #[Route('/{buildId}/build/addToCart', name: 'addBuild_ToCart')]
    public function addBuildToCart(#[MapEntity(id: 'buildId')] Build $build, Request $request): Response
    {
        $session = $request->getSession();
        // Retrieve the cart from the session or initialize it as an empty array
        $cart = $session->get('cart', []);
        // Get the product ID
        $buildId = $build->getId();
        // Check if the product is already in the cart
        if (isset($cart[$buildId])) {
            // Increment the quantity if the product already exists
            $cart[$buildId]['qtt'] += 1;
        } else {
            // Add the product to the cart with an initial quantity of 1
            $cart[$buildId] = [
                'qtt' => 1,
                'type' => 'build'
            ];
        }
        // Save the updated cart back to the session
        $session->set('cart', $cart);

        $this->addFlash(
            'success', 
            'Item successfully added to your cart.'
        );
        return $this->redirectToRoute('show_cart');
    }


    //  REMOVES A PRODUCT FROM THE CART REGARDLESS OF QUANTITY

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


    //  ADDS 1 TO QUANTITY OF A PRODUCT IN CART

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


    //  REMOVES 1 TO QUANTITY OF A PRODUCT IN CART

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


    //  COMPLETELY EMPTIES OUT THE CART

    #[Route('/user/cart/flush', name: 'flush_cart')]
    public function flushCart(Request $request): Response
    {
        $session = $request->getSession();
        $session->set('cart', []);
        return $this->redirectToRoute('show_cart');
    }
}
