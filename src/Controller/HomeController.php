<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{


    //  RETURNS THE HOMEPAGE

    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository, CategoryRepository $categoryRepository): Response
    {
        $popularCategories = [];
            $popularCategories["categ1"] = $categoryRepository->findOneBy(["name"=>"Processor"]);
            $popularCategories["categ2"] = $categoryRepository->findOneBy(["name"=>"Video card"]);
            $popularCategories["categ3"] = $categoryRepository->findOneBy(["name"=>"Memory"]);
            $popularCategories["categ4"] = $categoryRepository->findOneBy(["name"=>"Service"]);

        $trendingProducts = [];
            $trendingProducts = $productRepository->findTop5Products();

        return $this->render('home/homepage.html.twig', [
            'popularCategories' => $popularCategories,
            'trendingProducts' => $trendingProducts,
        ]);
    }
}
