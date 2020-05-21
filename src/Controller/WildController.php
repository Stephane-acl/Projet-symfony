<?php
// src/Controller/WildController.php
namespace App\Controller;

use App\Entity\Category;
use App\Entity\Episode;
use App\Entity\Program;
use App\Entity\Season;
use App\Form\ProgramSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class WildController extends AbstractController
{
    /**
     * @Route("/wild", name="wild_index")
     */
    public function index(Request $request): Response
    {
        $programs = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findAll();

        if (!$programs) {
            throw $this->createNotFoundException(
                'No program found in program\'s table.'
            );
        }

        $form = $this->createForm(
            ProgramSearchType::class,
            null,
            ['method' => Request::METHOD_GET]
        );

        return $this->render(
            'wild/index.html.twig', [
            'programs' => $programs,
            'form' => $form->createView(),
        ]);
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

        return $this->render('wild/show.html.twig', [
                'program' => $program,
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

        return $this->render('wild/category.html.twig', [
                'programs' => $programs,
                'categoryName' => $categoryName
            ]);
    }

    /**
     * @Route("/wild/program/{slug}",
     *      requirements={"slug"="[0-9-a-z]+$"},
     *      defaults={"slug"="Aucune série sélectionnée, veuillez choisir une série"},
     *      name="show_program")
     * @param string $slug
     * @return Response
     */
    public function showByProgram(string $slug): Response
    {
        $slug = ucwords(implode(" ", explode("-", $slug)));

        $program = $this->getDoctrine()
            ->getRepository(Program::class)
            ->findOneBy(['title' => mb_strtolower($slug)]);;
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with ' . $slug . ' title, found in program\'s table.'
            );
        }
        $seasonProgram = $program->getSeasons();

        return $this->render('wild/program.html.twig', [
                'slug' => $slug,
                'program' => $program,
                'seasons' => $seasonProgram
            ]);
    }

    /**
     * @Route("/wild/season/{id}",
     *      requirements={"slug"="[0-9-a-z]+$"},
     *      defaults={"id"= null},
     *      name="show_season")
     * @param int $id
     * @return Response
     */
    public function showBySeason(int $id): Response
    {
        $season = $this->getDoctrine()
            ->getRepository(Season::class)
            ->find($id);
        $program = $season->getProgram();
        $episodes = $season->getEpisodes();

        if (!$season) {
            throw $this->createNotFoundException(
                'No seasons found in season\'s table.'
            );
        }
        return $this->render('wild/season.html.twig', [
                'season' => $season,
                'program' => $program,
                'episodes' => $episodes
            ]);
    }

    /**
     * @Route("/wild/episode/{id}",
     *      name="show_episode")
     * @param Episode $episode
     * @return Response
     */
    public function showByEpisode(Episode $episode): Response //param converter
    {
        $season = $episode->getSeason();
        $program = $season->getProgram();

        if (!$episode) {
            throw $this->createNotFoundException(
                'No episode found in episode\'s table.'
            );
        }

        return $this->render('wild/episode.html.twig', [
                'episode' => $episode,
                'season' => $season,
                'program' => $program
            ]);
    }
}