<?php

namespace App\Controller;

use DateTime;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use PhpParser\Node\Stmt\Label;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    #[Route('/category/{categoryId}/products', name: 'app_product')]
    public function index(#[MapEntity(id: 'categoryId')] Category $category): Response
    {
        $products = $category->getProducts();
        return $this->render('product/index.html.twig', [
            'category' => $category,
            'products' => $products
        ]);
    }

    #[Route('/category/{categoryId}/products/{productId}/product', name: 'show_product')]
    public function showProduct(#[MapEntity(id: 'productId')] Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    // #[Route('/search_results', name: 'search_results')]
    // public function searchResults(Request $request)
    // {
    //     $session = $request->getSession();
    //     $msg = "bonjour";
    //     if (!$session->isStarted()) {
    //         $session->start();
    //     }
    //     return $this->render('product/search_results.html.twig', [
    //         'msg' => $msg,
    //     ]);
    // }

    //PRODUCT CONTROL PANEL FOR ADMIN USE
    #[Route('/control', name: 'control_product')]
    public function control(CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {
        $categories = $categoryRepository->findAll();
        $products = $productRepository->findAll();

        return $this->render('product/control.html.twig', [
            'categories' => $categories,
            'products' => $products
        ]);
    }

    #[Route('/control/{id}/processJson', name: 'process_json')]
    public function processJson(#[MapEntity(id: 'id')] Category $category, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository): Response 
    {
        $jsonList = [
            '../public/json/cpu.json',
            '../public/json/cpu-cooler.json',
            '../public/json/motherboard.json',
            '../public/json/memory.json',
            '../public/json/video-card.json',
            '../public/json/internal-hard-drive.json',
            '../public/json/external-hard-drive.json',
            '../public/json/case.json',
            '../public/json/power-supply.json',
            '../public/json/optical-drive.json',
            '../public/json/wired-network-card.json',
            '../public/json/wireless-network-card.json',
            '../public/json/sound-card.json',
            '../public/json/os.json',
            '../public/json/case-accessory.json',
            '../public/json/case-fan.json',
            '../public/json/fan-controller.json',
            '../public/json/headphones.json',
            '../public/json/keyboard.json',
            '../public/json/mouse.json',
            '../public/json/monitor.json',
            '../public/json/speakers.json',
            '../public/json/webcam.json',
            '../public/json/ups.json',
            '../public/json/thermal-paste.json'
        ];

        $jsonData = file_get_contents($jsonList[$category->getId()-1] );
        $products = json_decode($jsonData, true);
        $excludedProperties = ["name", "price"];

        foreach($products as $productData) {
            if(isset($productData["price"])) {
                $product = new Product();
                $product->setLabel($productData["name"]);
                $product->setPrice($productData["price"]);
                $product->setInStock(true);
                $filteredData = array_filter($productData, function($key) use ($excludedProperties) {
                    return !in_array($key, $excludedProperties, true);
                }, ARRAY_FILTER_USE_KEY);
                $product->setSpecs($filteredData);
                $categoryId = $category->getId();
                $matchingCategory = $categoryRepository ->find($categoryId);
                $product->setCategory($matchingCategory);

                $entityManager->persist($product);
                $entityManager->flush();
            }
        }
        
        $this->addFlash(
            'success', 
            'Database updated'
        );
        return $this->redirectToRoute('control_product');
    }
}