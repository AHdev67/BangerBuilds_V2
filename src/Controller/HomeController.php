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
            $popularCategories["categ3"] = $categoryRepository->findOneBy(["name"=>"Service"]);

        $newestProducts = [];
            $newestProducts["prod1"] = $productRepository->findOneBy(["label"=>"AMD Ryzen 7 7800X3D"]);
            $newestProducts["prod2"] = $productRepository->findOneBy(["label"=>"Gigabyte WINDFORCE OC GeForce RTX 4070 Ti SUPER"]);
            $newestProducts["prod3"] = $productRepository->findOneBy(["label"=>"Sapphire PULSE Radeon RX 7900 XT"]);
            $newestProducts["prod4"] = $productRepository->findOneBy(["label"=>"Lian Li O11 Dynamic EVO"]);
            $newestProducts["prod5"] = $productRepository->findOneBy(["label"=>"MSI B760 GAMING PLUS WIFI"]);

        return $this->render('home/homepage.html.twig', [
            'popularCategories' => $popularCategories,
            'newestProducts' => $newestProducts,
        ]);
    }
}
