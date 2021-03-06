<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Episode;
use App\Entity\User;
use App\Form\CommentType;
use App\Form\EpisodeType;
use App\Repository\EpisodeRepository;
use App\Service\Slugify;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/episode")
 */
class EpisodeController extends AbstractController
{
    /**
     * @Route("/", name="episode_index", methods={"GET"})
     */
    public function index(EpisodeRepository $episodeRepository): Response
    {
        return $this->render('episode/index.html.twig', [
            'episodes' => $episodeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="episode_new", methods={"GET","POST"})
     * @param Request $request
     * @param Slugify $slugify
     * @return Response
     */
    public function new(Request $request, Slugify $slugify): Response
    {
        $episode = new Episode();
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $slug = $slugify->generate($episode->getTitle());
            $episode->setSlug($slug);
            $entityManager->persist($episode);
            $entityManager->flush();
            $this->addFlash(
                'success',
                'Votre épisode est crée !'
            );
            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/new.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{slug}", name="episode_show", methods={"GET", "POST"})
     * @param Episode $episode
     * @param Request $request
     * @return Response
     */
    public function show(Episode $episode, Request $request): Response
    {
        $comment = new Comment();

        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy(['id' => $this->getUser()]);

        $comment->setAuthor($user);
        $comment->setEpisode($episode);

        $form = $this -> createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            return $this->redirectToRoute('episode_show', ['slug' => $episode->getSlug()]);
        }

        $comments = $this->getDoctrine()
            ->getRepository(Comment::class)
            ->findBy(['episode' => $episode]);
        dump($comments);
        return $this->render('episode/show.html.twig', [
            'episode' => $episode,
            'comments' => $comments,
            'form' => $form->CreateView()
        ]);
    }

    /**
     * @Route("/{slug}/edit", name="episode_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Episode $episode
     * @param Slugify $slugify
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Episode $episode, Slugify $slugify): Response
    {
        $form = $this->createForm(EpisodeType::class, $episode);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $episode->setSlug($slugify->generate($episode->getTitle()));
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash(
                'success',
                'Votre changement est sauvegardé !'
            );
            return $this->redirectToRoute('episode_index');
        }

        return $this->render('episode/edit.html.twig', [
            'episode' => $episode,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="episode_delete", methods={"DELETE"})
     * @param Request $request
     * @param Episode $episode
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Episode $episode): Response
    {
        if ($this->isCsrfTokenValid('delete'.$episode->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($episode);
            $entityManager->flush();
            $this->addFlash(
                'danger',
                'Votre episode est supprimé !'
            );
        }

        return $this->redirectToRoute('episode_index');
    }
}
