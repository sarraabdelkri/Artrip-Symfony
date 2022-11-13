<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\PostLike;
use App\Form\Evenement2Type;

use App\Repository\EvenementRepository;
use App\Repository\PostLikeRepository;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @Route("/evenement")
 */
class EvenementController extends Controller
{
    /**
     * @Route("/", name="evenement_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $Allevenements = $entityManager
            ->getRepository(Evenement::class)
            ->findAll();
        // Paginate the results of the query
        $evenements = $this->get('knp_paginator')->paginate(
        // Doctrine Query, not results
            $Allevenements,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            3
        );
        return $this->render('evenement/index.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("/front", name="indexfront", methods={"GET"})
     */
    public function indexfront(EvenementRepository $repository): Response
    {
        return $this->render('evenement/indexfront.html.twig', [
            'evenements' => $repository->findAll(),
        ]);

    }


    /**
     * @Route("/new", name="evenement_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

        $evenement = new Evenement();
        $form = $this->createForm(Evenement2Type::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $evenement->getImage();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            // moves the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('even_directory'),
                $fileName
            );

            // updates the 'brochure' property to store the PDF file name
            // instead of its contents
            $evenement->setImage($fileName);


            $entityManager->persist($evenement);
            $entityManager->flush();
            $this->addFlash('info', 'Evenement ajouté avec succées !');

            return $this->redirectToRoute('evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/new.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evenement_show", methods={"GET"})
     */
    public function show(Evenement $evenement): Response
    {
        return $this->render('evenement/show.html.twig', [
            'evenement' => $evenement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="evenement_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Evenement2Type::class, $evenement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $evenement->getImage();
            $fileName = md5(uniqid()) . '.' . $file->guessExtension();
            // moves the file to the directory where brochures are stored
            $file->move(
                $this->getParameter('even_directory'),
                $fileName
            );

            // updates the 'brochure' property to store the PDF file name
            // instead of its contents
            $evenement->setImage($fileName);
            $entityManager->flush();
            $this->addFlash('info', 'Evenement ajouté avec succées !');
            return $this->redirectToRoute('evenement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evenement/edit.html.twig', [
            'evenement' => $evenement,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/sorted",name="sorted")
     */
    public function sortedEvenement(EvenementRepository $repository, EvenementRepository $rep)
    {

        {
            return $this->render('evenement/indexfront.html.twig', [
                'evenements' => $repository->orderByDate(),
            ]);

        }
    }


    /**
     * @Route("/{id}", name="evenement_delete", methods={"POST"} ,requirements={"id"="\d+"})
     */
    public function delete(Request $request, Evenement $evenement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evenement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evenement_index', [], Response::HTTP_SEE_OTHER);
    }


    public function UpdateJoin(Evenement $evenement)
    {
        $part = $evenement->getNbrGoing();
        $part = $part + 1;
        $evenement->setNbrGoing($part);

        $this->getDoctrine()->getManager()->flush();


    }

    /**
     * @Route("/filter/Today", name="event_Today")
     */
    public function FilterToday(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())=0'
        );

        $evenements = new Evenement();
        $form = $this->createForm(Evenement2Type::class, $evenements);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenements->upload();
            $evenements->setNbrGoing($evenements->getNbrGoing());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenements);
            $entityManager->flush();
        }
        $evenements = $query->getResult();

        return $this->render('evenement/indexfront.html.twig', [
            'evenements' => $evenements,
        ]);
    }

    /**
     * @Route("/tri/triTrending", name="event_Trending")
     */
    public function TriTrending(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            ORDER BY E.nbrGoing ASC'
        );

        $MostSuccesful = $query->getResult();
        $evenements = new Evenement();
        $form = $this->createForm(Evenement2Type::class, $evenements);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenements->upload();
            $evenements->setNbrGoing($evenements->getNbrGoing());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenements);
            $entityManager->flush();
        }
        $events = $query->getResult();

        return $this->render('evenement/indexfront.html.twig', [
            'evenements' => $evenements,
            'MostSuccesful' => $MostSuccesful,
        ]);

    }

    /**
     * @Route("/filter/HasPassed", name="event_HasPassed")
     */
    public function FilterHasPassed(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
            WHERE DATE_DIFF(E.date,CURRENT_DATE())<0'
        );

        $evenements = new Evenement();
        $form = $this->createForm(Evenement2Type::class, $evenements);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenements->upload();
            $evenements->setNbrGoing($evenements->getNbrGoing());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenements);
            $entityManager->flush();
        }
        $evenements = $query->getResult();

        return $this->render('evenement/indexfront.html.twig', [
            'evenements' => $evenements,
        ]);

    }


    /**
     * Permet de liker ou disliker un article
     *
     * @Route ("/like/{id}", name="post_like")
     *
     * @param Evenement $evenement
     * @param PostLikeRepository $likeRepository
     * @return Response
     */
    public function like(Evenement $evenement, PostLikeRepository $likeRepository): Response
    {
        $user = $this->getEvenement();
        if (!$user)
            return $this->redirectToRoute('indexfront');


            if($evenement->isLikedByUser($user)){
            $like = $likeRepository->findOneBy([
                'Evenement' => $evenement,
                'nom_client' => $user
            ]);
        $em = $this->getDoctrine()->getManager();
        $em->remove($like);
        $em->flush();

        return $this->redirectToRoute('indexfront');

    }
        $like = new PostLike();
        $like->setEvenement($evenement)
            ->setNomClient($user);

        $em = $this->getDoctrine()->getManager();
        $em->persist($like);
        $em->flush();

        return $this->redirectToRoute('indexfront');

    }

}
