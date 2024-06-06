<?php

// namespace App\Service;

// use App\Form\SearchType;
// use Psr\Log\LoggerInterface;
// use Symfony\Component\Form\FormInterface;
// use Symfony\Component\Routing\RouterInterface;
// use Symfony\Component\Form\FormFactoryInterface;
// use Symfony\Component\HttpFoundation\RequestStack;

// class SearchService
// {
//     private $formFactory;
//     private $requestStack;
//     private $router;
//     private $logger;

//     public function __construct(FormFactoryInterface $formFactory, RequestStack $requestStack, RouterInterface $router, LoggerInterface $logger)
//     {
//         $this->formFactory = $formFactory;
//         $this->requestStack = $requestStack;
//         $this->router = $router;
//         $this->logger = $logger;
//     }

//     public function createSearchForm(): FormInterface
//     {
//         $form = $this->formFactory->create(SearchType::class, null, [
//             'method' => 'GET',
//             'action' => $this->router->generate('search_results'),
//         ]);

//         $request = $this->requestStack->getCurrentRequest();

//         if (!$request) {
//             throw new \LogicException('No current request.');
//         }

//         if (!$request->hasSession()) {
//             $this->logger->error('Session has not been set.');
//             throw new \LogicException('Session has not been set.');
//         }

//         $session = $request->getSession();

//         if (!$session->isStarted()) {
//             $this->logger->info('Starting session.');
//             $session->start();
//         }

//         $form->handleRequest($request);

//         if ($form->isSubmitted() && $form->isValid()) {
//             $data = $form->getData();
//             $session->set('search_query', $data['query']);
//         }

//         return $form;
//     }
// }