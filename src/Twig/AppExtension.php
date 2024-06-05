<?php

// namespace App\Twig;

// use App\Service\SearchService;
// use Twig\Extension\AbstractExtension;
// use Twig\TwigFunction;
// use Twig\Environment;

// class AppExtension extends AbstractExtension
// {
//     private $searchService;
//     private $twig;

//     public function __construct(SearchService $searchService, Environment $twig)
//     {
//         $this->searchService = $searchService;
//         $this->twig = $twig;
//     }

//     public function getFunctions()
//     {
//         return [
//             new TwigFunction('getSearchForm', [$this, 'getSearchForm']),
//         ];
//     }

//     public function getSearchForm()
//     {
//         return $this->searchService->createSearchForm()->createView();
//     }

//     public function addGlobalSearchForm()
//     {
//         $searchForm = $this->getSearchForm();
//         $this->twig->addGlobal('searchForm', $searchForm);
//     }
// }