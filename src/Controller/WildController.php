<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     */
    public function index(): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }
        return $this->render(
            'wild/index.html.twig',
            ['programs' => $programs]
        );
    }

    /**
     * @Route("/wild/show/{slug}",
     *      requirements={"slug"="[0-9-a-z]+$"},
     *      defaults={"slug"="Aucune série sélectionnée, veuillez choisir une série"},
     *      name="wild_show")
     * @param $slug
     * @return Response
     */
    public function show(string $slug): Response
    {
        if (!$slug) {
            throw $this
                ->createNotFoundException('No slug has been sent to find a program in program\'s table.');
        }
        $slug = ucwords(implode(" ", explode("-", $slug)));

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/show.html.twig',
            ['program' => $program,
                'slug' => $slug
            ]);
    }

    /**
     * @Route("/wild/category/{categoryName}",
     *     defaults={"categoryName"="Aucune catégorie sélectionnée, veuillez choisir une catégorie"},
     *     name="show_category")
     * @param string $categoryName
     * @return Response
     */
    public function showByCategory(string $categoryName)
    {
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findOneBy(['name' => mb_strtolower($categoryName)]);

        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findBy(
                ['category' => $category],
                ['id' => 'desc'],
                3
            );

        if (!$category) {
            throw $this->createNotFoundException(
                'No category with ' . $categoryName . 'in categories table.'
            );
        }
        if (!$programs) {
            throw $this->createNotFoundException(
                'No program with ' . $categoryName . ' title, found in program\'s table.'
            );
        }

        return $this->render('wild/category.html.twig',
            ['programs' => $programs,
                'categoryName' => $categoryName
            ]);
    }
}