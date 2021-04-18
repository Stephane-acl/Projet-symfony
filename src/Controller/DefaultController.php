<?php


namespace App\Controller;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
        public function index() :Response
        {
            return $this->render('home.html.twig');
        }

    /**
     * @Route("user/{id}", name="show_profile")
     * @param User $user
     * @return Response
     */
    public function showProfile(User $user): Response
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $user->getId()]);

        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }
}