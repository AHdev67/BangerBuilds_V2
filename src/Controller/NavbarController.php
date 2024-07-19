<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\SearchbarType;
use Psr\Log\LoggerInterface;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class NavbarController extends AbstractController
{

    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }


    //  RETURNS THE NAVBAR TEMPLATE FOR RENTERING IN THE BASE TEMPLATE

    #[Route('/navbar', name: 'app_navbar')]
    public function renderNavbar(Request $request): Response
    {
        return $this->render('navbar/navbar.html.twig', [
            'bing' => "bong",
        ]);
    }

    

    #[Route('/search', name: 'search_products')]
    public function search(Request $request): JsonResponse
    {
        $searchQuery = strtolower($request->query->get('searchQuery'));
        $results = [];

        $this->logger->info('searching for : ', ['searchQuery' => $searchQuery]);

        $results = $this->entityManager->getRepository(Product::class)->findProductsBySearchQuery($searchQuery);
        $this->logger->info('Products fetched', ['products' => $results]);

        return $this->json($results);
    }

    #[Route('/search/{slug}', name: 'show_results')]
    public function showResults($slug, PaginatorInterface $paginator, Request $request):Response
    {
        $searchQuery = strtolower($slug);

        $results = $paginator->paginate(
            $this->entityManager->getRepository(Product::class)->findProductsBySearchQuery($searchQuery),
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );

        return $this->render('product/show_results.html.twig', [
            'query' => $searchQuery,
            'results' => $results,
        ]);
    }

}