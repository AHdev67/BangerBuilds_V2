<?php

namespace App\Controller;

use App\Entity\Build;
use App\Form\BuildType;
use App\Entity\BuildComponent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BuildController extends AbstractController
{


    //  RETURNS THE CREATION FORM FOR A NEW BUILD / EDIT FORM FOR AN EXISITNG BUILD

    #[Route('/build/new', name: 'new_build')]
    public function newBuild(Build $build = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$build) {
            $build = new Build();
        }

        $form = $this->createForm(BuildType::class, $build);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cpu = $form->get('cpu')->getData();

            $buildComponentCpu = new BuildComponent();
            $buildComponentCpu->setComponent($cpu);
            $buildComponentCpu->setQuantity(1);
            $build->addBuildComponent($buildComponentCpu);

            $entityManager->persist($build);
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }

        return $this->render('build/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
