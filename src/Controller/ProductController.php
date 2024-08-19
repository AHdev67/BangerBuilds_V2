<?php

namespace App\Controller;

use DateTime;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\FilterType;
use App\Form\ProductType;
use PhpParser\Node\Stmt\Label;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{


    //  RETURNS LIST OF PRODUCTS BY CATEGORY

    #[Route('/category/{categoryId}/products', name: 'app_product')]
    public function index(#[MapEntity(id: 'categoryId')] Category $category, ProductRepository $productRepository, Array $filters = null, PaginatorInterface $paginator, Request $request): Response
    {
        if (!$filters){
            $filters = [
                "orderBy" => null,
                "filterByBrand" => null,
                "filterByGen" => null,
                "filterByModel" => null
            ];
            $products = $paginator->paginate(
                $productRepository->findByCategoryOrderedByRating($category),
                $request->query->getInt('page', 1), /*page number*/
                10 /*limit per page*/
            );
        }

        $form = $this->createForm(FilterType::class, $filters, ['category' => $category]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $filters = [
                "orderBy" => $form->get('orderBy')->getData(),
                "filterByBrand" => $form->get('filterByBrand')->getData(),
                "filterByGen" => $form->get('filterByGeneration')->getData(),
                "filterByModel" => $form->get('filterByModel')->getData(),
            ];

            return $this->redirectToRoute('app_product', ['categoryId'=>$category->getId()]);
        }
        
        return $this->render('product/list_products.html.twig', [
            'formFilters' => $form->createView(),
            'filters' => $filters,
            'category' => $category,
            'products' => $products
        ]);
    }


    //  RETURNS LIST OF SERVICES

    #[Route('/services', name: 'app_services')]
    public function listServices(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $services = $paginator->paginate(
            $productRepository->findByCategory(26),
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );
        
        return $this->render('product/list_services.html.twig', [
            'services' => $services
        ]);
    }

    //  RETURNS INFOPAGE OF A SPECIFIC PRODUCT (BY ID)

    #[Route('/{productId}/product', name: 'show_product')]
    public function show(#[MapEntity(id: 'productId')] Product $product): Response
    {
        $formattedSpecs = [];
        foreach ($product->getSpecs() as $key => $value) {
            if ($key == "smt") {
                $formattedKey = "SMT";
                $formattedValue = "Yes";
            }
            elseif ($key == "tdp") {
                $formattedKey = "TDP";
                $formattedValue = $value."W";
            }
            else {
                $formattedKey = $key;
                $formattedValue = $value;
            }

            $formattedSpecs[$formattedKey] = $formattedValue;
        }

        return $this->render('product/show_product.html.twig', [
            'product' => $product,
            'specs' => $formattedSpecs
        ]);
    }


    //  RETURNS AND PROCESSES EITHER THE PRODUCT CREATION FORM / THE EDIT FORM FOR A SPECIFIC PRODUCT (BY ID)

    #[Route('/control/newProduct', name: 'new_product')]
    #[Route('/control/{id}/edit', name: 'edit_product')]
    public function newEdit(Product $product = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$product) {
            $product = new Product();
            $product->setInStock(true);
        }
        
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $product = $form->getData();
            
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('app_product', ['categoryId'=>$product->getCategory()->getId()]);
        }

        return $this->render('product/new_product.html.twig', [
            'formAddProduct' => $form,
            'edit' => $product->getId(),
            'product' => $product
        ]);
    }

    //  DELETES AN EXISTING PRODUCT

    #[Route('/product/{id}/delete', name: 'delete_product')]
    public function delete(Product $product, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($product);
        $entityManager->flush();

        return $this->redirectToRoute('app_product', ['categoryId'=>$product->getCategory()->getId()]);
    }


    //  RETURNS THE PRODUCT CONTROL PANEL FOR ADMIN USERS ONLY

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

    #[Route('/control/addPlaceholders', name: 'add_placeholders')]
    public function addPlaceholders(EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        foreach ($products as $product) {
            if ($product->getDescription() !== null){
                $product->setDescription(
                "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis sollicitudin lectus ut ex dictum mollis. 
                Etiam ornare mauris nec lorem interdum, ut pellentesque dolor iaculis. 
                Aliquam erat volutpat. In et ligula tincidunt, sollicitudin turpis nec, tempus turpis. 
                In hac habitasse platea dictumst. Nunc ultrices ex nibh, in posuere diam aliquet eget. 
                Pellentesque accumsan arcu vel neque ultricies gravida. Etiam fringilla egestas ante, 
                in laoreet felis consectetur vel. Nam elementum eros et ligula faucibus tempus. 
                Curabitur porta dolor sit amet nisl pellentesque tempus."
                );
            }            
            $entityManager->persist($product);
        }

        $entityManager->flush();

        return $this->redirectToRoute('control_product');
    }

    #[Route('/control/{id}/fixGpus', name: 'fix_gpus')]
    public function fixGpuNames(#[MapEntity(id: 'id')] Category $category, EntityManagerInterface $entityManager, ProductRepository $productRepository): Response
    {
        $gpus = $category->getProducts();

        foreach ($gpus as $gpu) {
            $label = $gpu->getLabel();
            $chipset = $gpu->getSpecs()["chipset"];

            $gpu->setLabel($label . " " . $chipset);
            $entityManager->persist($gpu);

            // dd($gpu);
        }

        $entityManager->flush();

        return $this->redirectToRoute('control_product');
    }


    //  PROCESSES THE INTIAL JSON PRODUCT DATA AND CONVERTS IT TO PRODUCT OBJECTS THAT ARE PERSISTED TO THE DATABASE

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