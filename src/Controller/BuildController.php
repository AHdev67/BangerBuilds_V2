<?php

namespace App\Controller;

use App\Entity\Build;
use App\Form\BuildType;
use App\Entity\Category;
use App\Entity\BuildComponent;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BuildController extends AbstractController
{

    #[Route('/{buildId}/build', name: 'show_build')]
    public function show(#[MapEntity(id: 'buildId')] Build $build): Response
    {

        return $this->render('build/show_build.html.twig', [
            'build' => $build,
        ]);
    }

    //  RETURNS THE CREATION FORM FOR A NEW BUILD / EDIT FORM FOR AN EXISITNG BUILD

    #[Route('/build/new', name: 'new_build')]
    public function newBuild(Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {

        //  BUILD OBJECT INITIALIZATION

        $build = new Build();
        $build->setTotal(0);

        $form = $this->createForm(BuildType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $components = [
                $form->get('cpu')->getData(),
                $form->get('cpuCooler')->getData(),
                $form->get('motherboard')->getData(),
                $form->get('memory')->getData(),
                $form->get('graphicsCard')->getData(),
                $form->get('storage')->getData(),
                $form->get('powerSupply')->getData(),
                $form->get('case')->getData(),
                $form->get('opticalDrive')->getData(),
                $form->get('wiredNetworkCard')->getData(),
                $form->get('wirelessNetworkCard')->getData(),
                $form->get('soundCard')->getData(),
                $form->get('operatingSystem')->getData(),
                $form->get('caseFan')->getData(),
                $form->get('service')->getData(),
            ];

            foreach ($components as $component) {
                if (isset($component)) {
                    $buildComponent = new BuildComponent();
                    $buildComponent->setComponent($component);
                    $buildComponent->setQuantity(1);
                    $build->addBuildComponent($buildComponent);
                    $build->addToTotal($buildComponent->getComponent()->getPrice() * $buildComponent->getQuantity());
                }
                
            }

            $build->setAuthor($this->getUser());
            
            $entityManager->persist($build);
            $entityManager->flush();

            return $this->redirectToRoute('show_build', ['buildId'=>$build->getId()]);
        }

        return $this->render('build/new_build.html.twig', [
            'formBuild' => $form->createView(),
        ]);
    }
}
