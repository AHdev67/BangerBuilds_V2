<?php

namespace App\Controller;

use App\Entity\Build;
use App\Entity\Product;
use App\Form\BuildType;
use App\Entity\Category;
use Psr\Log\LoggerInterface;
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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig\SecondLevelCache\LoggerConfig;

class BuildController extends AbstractController
{
    private $entityManager;
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
    }
    

    // RETURNS THE LIST VIEW OF BUILDS WITH THE PREBUILT ATTRIBUTE SET TO TRUE

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


    // RETURNS THE DISPLAY VIEW FOR A BUILD

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

//-------------------------------------------------------------------------------------------------------------------------------------
//                                                      AJAX RESPONSE METHOD
//-------------------------------------------------------------------------------------------------------------------------------------


    // RETURNS THE LIST OF COMPATIBLE MOTHERBOARDS WITH THE SELECTED CPU

    #[Route('/get-motherboards', name: 'get_motherboards')]
    public function getMotherboards(Request $request): JsonResponse
    {
        $cpuId = $request->query->get('cpuId');
        $cpu = $this->entityManager->getRepository(Product::class)->find($cpuId);

        $motherboards = [];

        $this->logger->info('Fetched CPU', ['cpu' => $cpu]);

        // IF SELECTED CPU IS INTEL
        if ($cpu && strpos($cpu->getLabel(), 'Intel') !== false) {
            if ((
                // 12th, 13th & 14th gen Intel CPUs
                strpos($cpu->getLabel(), '-14') !== false || 
                strpos($cpu->getLabel(), '-13') !== false || 
                strpos($cpu->getLabel(), '-12') !== false
            )){
                $motherboards = $this->entityManager->getRepository(Product::class)->findMotherboardsBySocket('LGA1700');
                $this->logger->info('Motherboards fetched', ['motherboards' => $motherboards]);
            }

            else if ((
                // 10th & 11th gen Intel CPUs 
                strpos($cpu->getLabel(), '-11') !== false || 
                strpos($cpu->getLabel(), '-10') !== false
            )){
                $motherboards = $this->entityManager->getRepository(Product::class)->findMotherboardsBySocket('LGA1200');
                $this->logger->info('Motherboards fetched', ['motherboards' => $motherboards]);
            }

            else if ((
                // 6th, 7th,8th & 9th gen Intel CPUs
                strpos($cpu->getLabel(), '-9') !== false || 
                strpos($cpu->getLabel(), '-8') !== false ||
                strpos($cpu->getLabel(), '-7') !== false ||
                strpos($cpu->getLabel(), '-6') !== false
            )){
                $motherboards = $this->entityManager->getRepository(Product::class)->findMotherboardsBySocket('LGA1151');
                $this->logger->info('Motherboards fetched', ['motherboards' => $motherboards]);
            }
        }

        // ELSE IF SELECTED CPU IS AMD
        else if ($cpu && strpos($cpu->getLabel(), 'AMD') !== false) {
            if ((
                // Ryzen 7xxx
                strpos($cpu->getLabel(), 'Ryzen 9 7') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 7 7') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 5 7') !== false ||
                // Ryzen 8xxx
                strpos($cpu->getLabel(), 'Ryzen 9 8') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 7 8') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 5 8') !== false
            )){
                $motherboards = $this->entityManager->getRepository(Product::class)->findMotherboardsBySocket('AM5');
                $this->logger->info('Motherboards fetched', ['motherboards' => $motherboards]);
            }

            else if (
                strpos($cpu->getLabel(), 'Ryzen 9 5') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 7 5') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 5 5') !== false ||

                strpos($cpu->getLabel(), 'Ryzen 9 4') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 7 4') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 5 4') !== false ||

                strpos($cpu->getLabel(), 'Ryzen 9 3') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 7 3') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 5 3') !== false ||

                strpos($cpu->getLabel(), 'Ryzen 9 2') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 7 2') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 5 2') !== false ||

                strpos($cpu->getLabel(), 'Ryzen 9 1') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 7 1') !== false || 
                strpos($cpu->getLabel(), 'Ryzen 5 1') !== false
            ) {
                $motherboards = $this->entityManager->getRepository(Product::class)->findMotherboardsBySocket('AM4');
                $this->logger->info('Motherboards fetched', ['motherboards' => $motherboards]);
            }
        }

        return $this->json($motherboards);
    }
//-------------------------------------------------------------------------------------------------------------------------------------


    // RETURNS THE LIST OF COMPATIBLE CPUS WITH THE SELECTED MOTHERBOARD

    #[Route('/get-cpus', name: 'get_cpus')]
    public function getCpus(Request $request): JsonResponse
    {
        $moboId = $request->query->get('moboId');
        $motherboard = $this->entityManager->getRepository(Product::class)->find($moboId);

        $cpus = [];

        $this->logger->info('Fetched motherboard', ['motherboard' => $motherboard]);

        if (in_array("LGA1700", $motherboard->getSpecs())) {
            $cpus = array_merge(
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('Intel', '-14'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('Intel', '-13'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('Intel', '-12')
            );
            $this->logger->info('CPUs fetched', ['cpus' => $cpus]);
        }
        else if (in_array("LGA1200", $motherboard->getSpecs())) {
            $cpus = array_merge(
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('Intel', '-11'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('Intel', '-10')
            );
            $this->logger->info('CPUs fetched', ['cpus' => $cpus]);
        }
        else if (in_array("LGA1151", $motherboard->getSpecs())) {
            $cpus = array_merge(
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('Intel', '-9'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('Intel', '-8'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('Intel', '-7'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('Intel', '-6')
            );
            $this->logger->info('CPUs fetched', ['cpus' => $cpus]);
        }

        else if (in_array("AM5", $motherboard->getSpecs())) {
            $cpus = array_merge(

                // Zen4
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 9 7'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 7 7'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 5 7'),

                // Also Zen4
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 9 8'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 7 8'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 5 8')
            );
            $this->logger->info('CPUs fetched', ['cpus' => $cpus]);
        }
        else if (in_array("AM4", $motherboard->getSpecs())) {
            $cpus = array_merge(

                // Zen 3
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 9 5'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 7 5'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 5 5'),

                // Zen 2
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 9 4'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 7 4'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 5 4'),

                // Zen + & Zen 2
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 9 3'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 7 3'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 5 3'),

                // Zen & Zen +
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 9 2'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 7 2'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 5 2'),

                // Zen
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 9 1'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 7 1'),
                $this->entityManager->getRepository(Product::class)->findCpusByGeneration('AMD', 'Ryzen 5 1'),
            );
            $this->logger->info('CPUs fetched', ['cpus' => $cpus]);
        }

        // dd($cpus);

        return $this->json($cpus);
    }
}
