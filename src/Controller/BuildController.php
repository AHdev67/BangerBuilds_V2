<?php

namespace App\Controller;

use App\Entity\Build;
use App\Form\BuildType;
use App\Entity\Category;
use App\Entity\BuildComponent;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BuildController extends AbstractController
{


    //  RETURNS THE CREATION FORM FOR A NEW BUILD / EDIT FORM FOR AN EXISITNG BUILD

    #[Route('/build/new', name: 'new_build')]
    public function newBuild(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {

        //  BUILD OBJECT INITIALIZATION

        $build = new Build();

        $cpuSlot = new BuildComponent();
        $cpuCategory = $categoryRepository->findOneBy(['name' => 'Processor']);
        $cpuSlot->setCategory($cpuCategory);
        $build->addBuildComponent($cpuSlot);

        $cpuCoolerSlot = new BuildComponent();
        $build->addBuildComponent($cpuCoolerSlot);

        // dd($build);
        
        // $form = $this->createForm(BuildType::class, $build, ['categories' => $categories]);
        $form = $this->createForm(BuildType::class, $build);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cpuComponents = $form->get('cpu')->getData();

            foreach ($cpuComponents as $cpuComponent) {
                $cpuComponent->setQuantity(1);
                $build->addBuildComponent($cpuComponent);
            }

            $entityManager->persist($build);
            $entityManager->flush();

            return $this->redirectToRoute('app_user');
        }

        return $this->render('build/new_build.html.twig', [
            'formBuild' => $form->createView(),
        ]);
    }
}
