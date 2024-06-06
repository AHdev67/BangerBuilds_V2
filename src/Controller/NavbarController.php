<?php

namespace App\Controller;

use App\Form\ProductAutocompleteField;
use App\Form\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NavbarController extends AbstractController
{
    #[Route('/navbar', name: 'app_search')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            echo($request);
        }

        return $this->render('navbar/navbar.html.twig', [
            'searchForm' => $form,
        ]);
    }
}

// namespace App\Controller;

// use DateTime;
// use App\Entity\Product;
// use App\Entity\Category;
// use App\Form\ProductType;
// use PhpParser\Node\Stmt\Label;
// use App\Repository\ProductRepository;
// use App\Repository\CategoryRepository;
// use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\Routing\Attribute\Route;
// use Symfony\Bridge\Doctrine\Attribute\MapEntity;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// class NavbarController extends AbstractController
// {
//     #[Route('/navbar', name: 'app_nav')]
//     public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository)
//     {
        
//     }
// }
