<?php

namespace App\Controller;

use App\Form\SearchbarType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NavbarController extends AbstractController
{


    //  RETURNS THE NAVBAR TEMPLATE FOR RENTERING IN THE BASE TEMPLATE

    #[Route('/navbar', name: 'app_navbar')]
    public function renderNavbar(Request $request): Response
    {
        return $this->render('navbar/navbar.html.twig', [
            'bing' => "bong",
        ]);
    }

    

    // #[Route('/navbar/search', name: 'search_products')]
    // public function search(ProductRepository $productRepository, Request $request): JsonResponse
    // {
    //     $query = $request->query->get('searchQuery');
    //     $products = $productRepository->searchByName($query);

    //     return new JsonResponse($products);
    // }
}