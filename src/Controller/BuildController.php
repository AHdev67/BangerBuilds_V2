<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BuildController extends AbstractController
{
    #[Route('/build', name: 'app_build')]
    public function index(): Response
    {
        return $this->render('build/new_build.html.twig', [
            'controller_name' => 'BuildController',
        ]);
    }
}
