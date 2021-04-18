<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category/add", name="category_add")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findName();

        if (!$categories) {
            throw $this->createNotFoundException(
                'No category found in categorie\'s table.'
            );
        }

        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('category_add');
        }

        return $this->render(
            'category/add.html.twig', [
            'categories' => $categories,
            'form' => $form->createView()
        ]);
    }
}