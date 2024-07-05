<?php

namespace App\Controller;

use DateTime;
use App\Entity\Review;
use App\Entity\Product;
use App\Form\ReviewType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReviewController extends AbstractController
{
    #[Route('/review', name: 'app_review')]
    public function index(): Response
    {
        return $this->render('review/index.html.twig', [
            'controller_name' => 'ReviewController',
        ]);
    }

    #[Route('/product/{productId}/newReview', name: 'new_review')]
    #[Route('/product/{id}/edit', name: 'edit_review')]
    public function newEdit(#[MapEntity(id: 'productId')] Product $product, Review $review = null, Request $request, EntityManagerInterface $entityManager): Response
    {
        if (!$review) {
            $review = new Review();
        }
        
        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            
            $review = $form->getData();

            $review->setReviewDate(new DateTime());
            $review->setAuthor($this->getUser());
            $review->setReviewedProduct($product);

            $entityManager->persist($review);
            $entityManager->flush();

            return $this->redirectToRoute('show_product', ['productId'=>$product->getId()]);
        }

        return $this->render('review/new_review.html.twig', [
            'formAddReview' => $form,
            'product' => $product
        ]);
    }
}
