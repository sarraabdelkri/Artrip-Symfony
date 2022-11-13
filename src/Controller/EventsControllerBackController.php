<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Entity\PostLike;
use App\Form\EvenementType;
use App\Repository\CalendarRepository;
use App\Repository\PostLikeRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\EvenementRepository;
use App\Entity\Evenement;

use Symfony\Component\HttpFoundation\Request;

use Dompdf\Dompdf;
use Dompdf\Options;

use Symfony\Component\Serializer\SerializerInterface;


class EventsControllerBackController extends AbstractController
{


    const  ATTRIBUTES_TO_SERIALIZE = ['id', 'titre', 'lieu', 'description','type','nbrmaxpart'];

    /**
     * @Route("/events/controller/back", name="events_controller_back")'
     */
    public function index(PaginatorInterface $paginator , Request $request): Response
    {
        $evenement= $paginator->paginate($this->getDoctrine()->getRepository(Evenement::class)->findAllVisibleQuery()
            , $request->query->getInt('page', 1),4

        );
        return $this->render("events_controller_back/index.html.twig",
            array('evenements'=>$evenement));
    }

    /**
     * @Route("/addevenement",name="addevenement")
     */
    public function add(Request $request  ): Response
    {
        $evenement= new Evenement();
        $form= $this->createForm(EvenementType::class,$evenement);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()   ){
            $em = $this->getDoctrine()->getManager();
            $em->persist($evenement);

        }
        return $this->render("events_controller_back/add.html.twig",array("formEvenement"=>$form->createView()));
    }
     /**
     * @Route("/removeevenement/{id}",name="removeevenement")
     */
    public function delete(Request $request, Evenement $evenement, EvenementRepository $evenementRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $evenement->getId(), $request->request->get('_token'))) {
            $evenementRepository->
            remove($evenement);

        }

        return $this->redirectToRoute('events_controller_back', [], Response::HTTP_SEE_OTHER);
    }
      /**
     * @Route("/modifyevenement/{id}",name="modifyevenement")
     */
    public function update(Request $request, Evenement $evenement, EntityManagerInterface $entityManager){
        $form = $this->createForm(EvenementType::class, $evenement);
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
            return $this->redirectToRoute("events_controller_back");
        }

        return $this->render("events_controller_back/modify.html.twig",array("formEvenement"=>$form->createView()));
    }
    /**
     * * @Route("/stats", name="stats", methods={"GET","POST"})
     */

    public function statistiques(EvenementRepository $evenementRepository)

    {
        // On va chercher toutes les menus
        $menus = $evenementRepository->findAll();

//Data Category
        $randonnée = $evenementRepository->createQueryBuilder('a')
            ->select('count(a.id)')
            ->Where('a.type= :typeEvenement')
            ->setParameter('typeEvenement', "randonnée")
            ->getQuery()
            ->getSingleScalarResult();

        $camping = $evenementRepository->createQueryBuilder('a')
            ->select('count(a.id)')
            ->Where('a.type= :typeEvenement')
            ->setParameter('typeEvenement', "camping")
            ->getQuery()
            ->getSingleScalarResult();
        $kayak = $evenementRepository->createQueryBuilder('a')
            ->select('count(a.id)')
            ->Where('a.type= :typeEvenement')
            ->setParameter('typeEvenement', " kayak")
            ->getQuery()
            ->getSingleScalarResult();


        return $this->render('evenement/stats.html.twig', [
            'nrndonne' => $randonnée,

            'ncamping' => $camping,
            'nkayak' => $kayak,
        ]);
    }

    /**
     * @Route("/evenementlist",name="evenementlist")
     */
    public function list(PaginatorInterface $paginator , Request $request )
    {

        $evenement= $paginator->paginate($this->getDoctrine()->getRepository(Evenement::class)->findAllVisibleQuery()
            , $request->query->getInt('page', 1),4

        );
        return $this->render("events_controller_back/index.html.twig",
            array('evenements'=>$evenement));
    }

    /**
     * @Route("/evenementlistfront",name="evenementlistfront")
     */
    public function listfront(EvenementRepository $repository)
    {

        return $this->render('events_controller_back/indexfront.html.twig', [
            'evenements' => $repository->findAll(),
        ]);

    }
    /**
     * @Route("/addevenementfront",name="addevenementfront")
     */
    public function addfront(Request $request , EntityManagerInterface $entityManager): Response
    {
        $evenement = new Evenement();
        $form = $this->createForm(EvenementType::class, $evenement);
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


            $this->addFlash('success', 'Commentaire créé avec succès Attendez la confirmation de lAdministrateur ');
            return $this->redirectToRoute("events_controller_back");
        }
        return $this->render("events_controller_back/addfront.html.twig",array("formEvenement"=>$form->createView()));
    }
    // back template

    /**
     * @Route("/fullcalendar", name="fullcalendar")
     */
    public function Evenementback(EvenementRepository $calendar)
    {
        $rdvs = [];
        $events = $calendar->findAll();
        foreach ($events as $event)
        {

            $rdvs[] = [
                'id' => $event->getId(),
                'title' => $event->getTitre(),
                'description' => $event->getDescription(),
                'lieu' => $event->getLieu(),
                'start' => $event->getDate()->format('Y-m-d H:i:s'),


            ];

        }
        $data = json_encode($rdvs);
        return $this->render('events_controller_back/fullcalendrier.html.twig', compact('data'));
    }
    /**
     * @Route("/fullcalendarfront", name="fullcalendarfront")
     */
    public function Evenementfront(CalendarRepository $calendar)
    {

        $events = $calendar->findAll();

        $rdvs = [];

        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStart()->format('Y-m-d H:i:s'),
                'end' => $event->getEnd()->format('Y-m-d H:i:s'),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'backgroundColor' => $event->getBackgroundColor(),
                'borderColor' => $event->getBorderColor(),
                'textColor' => $event->getTextColor(),
                'allDay' => $event->getAllDay(),
            ];
        }



        $data = json_encode($rdvs);
        return $this->render('events_controller_back/fullcalendarfront.html.twig', compact('data'));
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
        $form = $this->createForm(EvenementType::class, $evenements);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $evenements->upload();
            $evenements->setNbrGoing($evenements->getNbrGoing());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($evenements);
            $entityManager->flush();
        }
        $evenements = $query->getResult();

        return $this->render('events_controller_back/indexfront.html.twig', [
            'evenements' => $evenements,
        ]);
    }
    /**
     * @Route("/filter/ThisWeek", name="event_ThisWeek")
     */
    public function FilterThisWeek(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $query = $em->createQuery(
            'SELECT E FROM App\Entity\Evenement E 
                WHERE DATE_DIFF(E.date,CURRENT_DATE())<7 AND DATE_DIFF(E.date,CURRENT_DATE())>0'
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

        return $this->render('events_controller_back/indexfront.html.twig', [
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
            ORDER BY E.nbrmaxpart DESC'
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

        return $this->render('events_controller_back/indexfront.html.twig', [
            'evenements' => $evenements,
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

        return $this->render('events_controller_back/indexfront.html.twig', [
            'evenements' => $evenements,
        ]);

    }
    /**
     * Permet de liker ou disliker un article
     *
     * @Route ("/like/{id}", name="post_like")
     *
     * @param PostLikeRepository $likeRepository
     * @return Response
     */
    public function like(int $id,PostLikeRepository $likeRepository, UserRepository $userRepository,EvenementRepository $evenementReporitory): Response
    {
        $evenement=$evenementReporitory->find(14);
        $user=$userRepository->find(7);

        if(!$user)

            return $this->redirectToRoute('evenementlistfront');
        if($evenement->isLikedByUser($user)){
            $like = $likeRepository->findOneBy([
                'nom_client' => $user->getNomUser(),
                'Evenement' => $evenement
            ]);
            $em = $this->getDoctrine()->getManager();
            $em->persist($like);
            $em->flush();

            return $this->redirectToRoute('evenementlistfront');

        }
        $like = new PostLike();
        $like->setEvenement($evenement)
            ->setNomClient($user);

        $em = $this->getDoctrine()->getManager();
        $em->remove($like);
        $em->flush();

        return $this->redirectToRoute('evenementlistfront');

    }
    /**
     * @Route("/pdf", name="pdf", methods={"GET"})
     */
    public function pdf(EvenementRepository $evenementRepository): Response
    {
        // Configure Dompdf according to your needs
        $pdfoptions = new Options();
        $pdfoptions->set('defaultFont', 'Arial');
        $pdfoptions->set('tempDir', '.\www\DaryGym\public\uploads\images');


        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfoptions);
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('events_controller_back/pdf.html.twig', [
            'evenements' => $evenementRepository->findAll(),
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
     * @Route("/ajouter/evenement" , name="evenement_ajouter" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function ajouter(Request $request, SerializerInterface $serializer)
    {


        $evenement = new Evenement();
        $titre = $request->query->get('titre');
        $lieu = $request->query->get('lieu');
        $description = $request->query->get('description');

        $type = $request->query->get('type');
      //  $date = $request->query->get('date');
          $nbrmaxpart= $request->query->get('nbrmaxpart');
        $em = $this->getDoctrine()->getManager();

        $evenement->setTitre( $titre);
        $evenement->setLieu(  $lieu);
        $evenement->setDescription(  $description);

         $evenement->setType($type);

       // $evenement ->setDate (new \DateTime());
        $evenement->setNbrmaxpart(  $nbrmaxpart);
        $em->persist($evenement);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($evenement);
        return new JsonResponse($formatted);
    }


    /**
     * @Route("/modifier/evenement" , name="hebergement_evenement" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */
    public function modifier(Request $request, SerializerInterface $serializer, EvenementRepository $repo)
    {
        $evenement = new Evenement();
        $titre = $request->query->get('titre');
        $lieu = $request->query->get('lieu');
        $description = $request->query->get('description');

        $type = $request->query->get('type');
        //  $date = $request->query->get('date');
        $nbrmaxpart= $request->query->get('nbrmaxpart');
        $em = $this->getDoctrine()->getManager();

        $evenement->setTitre( $titre);
        $evenement->setLieu(  $lieu);
        $evenement->setDescription(  $description);

        $evenement->setType($type);

        // $evenement ->setDate (new \DateTime());
        $evenement->setNbrmaxpart(  $nbrmaxpart);
        $em->persist($evenement);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($evenement);
        return new JsonResponse($formatted);
    }


    /**
     * @Route("/afficher/evenement" , name="evenement_afficher" ,  methods={"GET", "POST"}, requirements={"id":"\d+"})
     */

    public function afficher(Request $request, SerializerInterface $serializer, EvenementRepository $repo)
    {

        $evenements = $repo->findOneById($request->query->get('id'));
        $json = $serializer->serialize($evenements, 'json', ['groups' => ['evenement']]);
        //tbadel lite hebergement badlou forme jsn


        return $this->json(['evenement' => $evenements], Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);


    }

    /**
     * @Route("/delete/json", name="supprimer_evenement")
     */
    public function supprimerEvenement(Request $request, EvenementRepository $repo): Response
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
     * @Route("/even/list")
     * @param EvenementRepository $repo
     */
    public function getList(EvenementRepository  $repo, SerializerInterface $serializer): Response
    {

        $evenements = $repo->findAll();
        $json = $serializer->serialize($evenements, 'json', ['groups' => ['evenement']]);



        return $this->json(['evenement' => $evenements], Response::HTTP_OK, [], [
            'attributes' => self::ATTRIBUTES_TO_SERIALIZE
        ]);


    }

}
