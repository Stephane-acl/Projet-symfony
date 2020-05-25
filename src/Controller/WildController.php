<?php
// src/Controller/WildController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

    /**
     * @Route("/wild", name="wild_")
     */

class WildController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('wild/index.html.twig', [
            'website' => 'Wild Séries',
        ]);
    }

    /**
     * @Route("/show/{slug}",
     *      requirements={"slug"="[0-9-a-z]+$"},
     *      defaults={"slug"="Aucune série sélectionnée, veuillez choisir une série"},
     *      name="wild_show")
     * @param $slug
     * @return Response
     */
    public function show($slug): Response
    {
        $slug = ucwords(implode(" ", explode("-", $slug)));
        return $this->render('wild/show.html.twig', ['slug' => $slug]);
    }
}
