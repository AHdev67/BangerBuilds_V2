<?php

namespace App\Controller;

use App\Entity\Build;
use App\Entity\Product;
use App\Form\BuildType;
use App\Entity\Category;
use App\Entity\BuildComponent;
use App\Form\ComponentSlotType;
use App\Repository\BuildRepository;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BuildController extends AbstractController
{
    #[Route('/prebuilts', name: 'app_prebuilts')]
    public function index(PaginatorInterface $paginator, BuildRepository $buildRepository, Request $request): Response
    {
        $prebuilts = $paginator->paginate(
            $prebuilts = $buildRepository->findAllPrebuilts(),
            $request->query->getInt('page', 1), /*page number*/
            8 /*limit per page*/
        );
        
        return $this->render('build/list_prebuilts.html.twig', [
            'prebuilts' => $prebuilts
        ]);
    }



    #[Route('/{buildId}/build', name: 'show_build')]
    public function show(#[MapEntity(id: 'buildId')] Build $build): Response
    {
        return $this->render('build/show_build.html.twig', [
            'build' => $build,
        ]);
    }


    //  RETURNS THE CREATION FORM FOR A NEW BUILD / EDIT FORM FOR AN EXISITNG BUILD

    #[Route('/build/new', name: 'new_build')]
    #[Route('/build/{id}/edit', name: 'edit_build')]

    public function newBuild(Build $build = null ,Request $request, EntityManagerInterface $entityManager, CategoryRepository $categoryRepository, ProductRepository $productRepository): Response
    {

//---------------------------------------------------------------------------------------------------------------------------------------
        // if(!$build){
        //     $build = new Build();
        //     $build->setTotal(0);

        //     $categories = [
        //         $categoryRepository->findOneBy(['name' => "Processor"]),
        //         $categoryRepository->findOneBy(['name' => "CPU cooler"]),
        //         $categoryRepository->findOneBy(['name' => "Motherboard"]),
        //         $categoryRepository->findOneBy(['name' => "Memory"]),
        //     ];
            
        //     foreach ($categories as $category) {
        //         $component = new BuildComponent();
        //         $component->setCategory($category);
        //         $build->addBuildComponent($component);
        //     }

        //     } else {
        //         $categories = [];
        //         foreach ($build->getBuildComponents() as $buildComponent) {
        //             $categories[] = $buildComponent->getCategory();
        //         }
        //     }

        //     // Fetch products based on categories
        //     $productsByCategory = [];
        //     foreach ($categories as $category) {
        //     $productsByCategory[$category->getId()] = $productRepository->findBy(['category' => $category]);
        // }

        // $form = $this->createForm(BuildType::class, $build, ['products_by_category' => $productsByCategory]);

        // $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        //     $build = $form->getData();
        // }
//---------------------------------------------------------------------------------------------------------------------------------------
       
        if(!$build){
            $build = new Build();
            $build->setTotal(0);        

        }

        $form = $this->createForm(BuildType::class, $build);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $build = $form->getData();

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
//---------------------------------------------------------------------------------------------------------------------------------------
            $build->setAuthor($this->getUser());
            
            $entityManager->persist($build);
            $entityManager->flush();

            return $this->redirectToRoute('show_build', ['buildId'=>$build->getId()]);
        }

        return $this->render('build/new_build.html.twig', [
            'formBuild' => $form->createView(),
            'edit' => $build->getId(),
            'build' => $build
        ]);
    }

    #[Route('/build/{id}/delete', name: 'delete_build')]
    public function delete(Build $build, EntityManagerInterface $entityManager)
    {
        $entityManager->remove($build);
        $entityManager->flush();

        return $this->redirectToRoute('app_user');
    }


    public function getMotherboards(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $cpuId = $request->query->get('cpuId');
        $cpu = $this->$entityManager->getRepository(Product::class)->find($cpuId);

        $motherboards = [];

        if ($cpu && strpos($cpu->getLabel(), 'Intel') !== false) {
            $motherboards = $this->$entityManager->getRepository(Product::class)->createQueryBuilder('p')
                ->where('p.category = :category')
                ->andWhere('p.label LIKE :pattern')
                ->setParameter('category', 2)
                ->setParameter('pattern', 'Z%')
                ->getQuery()
                ->getResult();
        }

        return $this->json($motherboards);
    }
}
