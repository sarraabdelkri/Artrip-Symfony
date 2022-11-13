<?php

namespace App\Controller;

use
    App\Entity\Reservationeven;
use App\Form\ReservationevenType;
use App\Repository\EvenementRepository;
use App\Repository\ReservationevenRepository;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @Route("/res")
 */
class ReservationevenController extends AbstractController
{
    const  ATTRIBUTES_TO_SERIALIZE = ['id', 'nompartici', '	type', 'lieu','nomEvenement','date'];
    /**
     * @Route("/", name="reservationeven_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {$reservationevens = $entityManager
            ->getRepository(Reservationeven::class)
            ->findAll();

        return $this->render('reservationeven/index.html.twig', [
            'reservationevens' => $reservationevens,
        ]);
    }

    /**
     * @Route("/new", name="reservationeven_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $reservationeven = new Reservationeven();
        $form = $this->createForm(ReservationevenType::class, $reservationeven);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservationeven);
            $entityManager->flush();
            $this->addFlash('info', 'Reservation ajouté avec succées !');
            return $this->redirectToRoute('reservationeven_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservationeven/new.html.twig', [
            'reservationeven' => $reservationeven,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservationeven_show", methods={"GET"})
     */
    public function show(Reservationeven $reservationeven): Response
    {
        return $this->render('reservationeven/show.html.twig', [
            'reservationeven' => $reservationeven,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reservationeven_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Reservationeven $reservationeven, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationevenType::class, $reservationeven);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('info', 'Reservation ajouté avec succées !');
            return $this->redirectToRoute('reservationeven_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservationeven/edit.html.twig', [
            'reservationeven' => $reservationeven,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservationeven_delete", methods={"POST"})
     */
    public function delete(Request $request, Reservationeven $reservationeven, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservationeven->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservationeven);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reservationeven_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("sortedReservation",name="sortedReservation")
     */

    public function sortedReservation(Request $req, PaginatorInterface $pag)
    {

        $reservationevens = $pag->paginate($this->getDoctrine()->getRepository(Reservationeven ::class)->orderByDate()
            , $req->query->getInt('page', 1), 30

        );
        return $this->render('reservationeven/index.html.twig', [
            'reservationevens' => $reservationevens,
        ]);
    }


    /**
     * @Route("/pdf", name="pdf", methods={"GET"})
     */
    public function pdf(ReservationevenRepository $reservationevenRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfoptions = new Options();
        $pdfoptions->set('defaultFont', 'Arial');
        $pdfoptions->set('tempDir', '.\www\DaryGym\public\uploads\images');


        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfoptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('reservationevens/pdf.html.twig', [
            'reservationevens' => $reservationevenRepository->findAll(),
        ]);
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A4', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (inline view)
        $dompdf->stream("mypdf.pdf", [
            "Attachment" => false
        ]);
    }


    /**
     * @Route("/ajouter/reservationeven" , name="reservationeven_ajouter" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function ajouter(Request $request, SerializerInterface $serializer)
    {


        $reservationeven = new Reservationeven();
        $nompartici = $request->query->get('nompartici');
        $type = $request->query->get('type');

        $nomEvenement = $request->query->get('nomEvenement');
        $date = $request->query->get('date');


        $em = $this->getDoctrine()->getManager();

        $reservationeven->setNompartici($nompartici);
        $reservationeven->setType( $type);

        $reservationeven->setNomevenement(   $nomEvenement);
        $reservationeven ->setDate (new \DateTime());



        $em->persist($reservationeven);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($reservationeven);
        return new JsonResponse($formatted);
    }


    /**
     * @Route("/modifier/reservationeven" , name="reservationeven_evenement" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function modifier(Request $request, SerializerInterface $serializer, ReservationevenRepository $repo)
    {
        $reservationeven = new Reservationeven();
        $nompartici = $request->query->get('nompartici');
        $type = $request->query->get('type');

        $nomEvenement = $request->query->get('nomEvenement');
        $date = $request->query->get('date');

        $em = $this->getDoctrine()->getManager();

        $reservationeven->setNompartici($nompartici);
        $reservationeven->setType( $type);
        $reservationeven->setNomevenement(   $nomEvenement);
        $reservationeven ->setDate (new \DateTime());



        $em->persist($reservationeven);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($reservationeven);
        return new JsonResponse($formatted);
    }




    /**
     * @Route("/afficher/reservationeven" , name="reservationeven_afficher" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */

    public function afficher(Request $request, SerializerInterface $serializer, EvenementRepository $repo)
    {

        $reservationevens = $repo->findOneById($request->query->get('id'));
        $json = $serializer->serialize($reservationevens, 'json', ['groups' => ['reservationeven']]);
        //tbadel lite hebergement badlou forme jsn


        return $this->json(['reservationeven' => $reservationevens], Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);


    }

    /**
     * @Route("/delete/json", name="supprimer_reservationeven")
     */
    public function supprimerReservationeven(Request $request, ReservationevenRepository  $repo): Response
    {

        $id = $request->get("id");
        $em = $this->getDoctrine()->getManager();

        $id = $repo->find($id);

        if ($id != null) {
            $em->remove($id);
            $em->flush();
            $serializer = new Serializer([new ObjectNormalizer()]);
            $formatted = $serializer->normalize("les informations ont ete supprimer ");
            return new JsonResponse($formatted);
        }

        return new JsonResponse("Id Invalide");
    }
    /**
     * @Route("/reservationeven/list")
     * @param ReservationevenRepository $repo
     */
    public function getList(ReservationevenRepository  $repo, SerializerInterface $serializer): Response
    {

        $reservationevens = $repo->findAll();
        $json = $serializer->serialize($reservationevens, 'json', ['groups' => ['reservationeven']]);



        return $this->json(['evenement' => $reservationevens], Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);


    }
}
