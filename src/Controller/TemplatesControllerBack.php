<?php

namespace App\Controller;

use App\Entity\Reservation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Seance;
use App\Form\SeanceType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\SeanceRepository;
use App\Repository\ReservationRepository;

class TemplatesControllerBack extends AbstractController
{
   
    #[Route('/rb', name: 'app_reservationb', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,SeanceRepository $seanceRepository): Response
    {
        $seance = new Seance();
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);
        $stats = $seanceRepository->getSeanceStatsByDayOfWeek();
         // Récupérez le paramètre de recherche depuis la requête
          $query = $request->query->get('q');

           if ($query)
         {
               // Si une requête de recherche est soumise, récupérez les séances correspondantes depuis la base de données
           $seancesres = $seanceRepository->findSeanceByNom($query); 
         } 
         else
          {
        // Sinon, récupérez toutes les séances
        $seancesres = $seanceRepository->findAll();
         }
        if ($form->isSubmitted() && $form->isValid()) {
            
            $imageFile = $form->get('imageseance')->getData();
            if ($imageFile) {
                // Move uploaded file to desired location
                // Example:
                $uploadsDirectory = $this->getParameter('uploads_directory');
                $imageFileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $uploadsDirectory,
                    $imageFileName
                );
                // Set image file name to user entity
                $seance->setImageseance($imageFileName);
            }
            $entityManager->persist($seance);
            $entityManager->flush();

            return $this->redirectToRoute('app_reservationb', [], Response::HTTP_SEE_OTHER);
        }
        else
        {
            dump($form->getErrors(true, false));
            $seances = $seanceRepository->findAll();

        return $this->renderForm('seance/seance_elements.html.twig', [
            'seance' => $seance,
            'seances' => $seances,
            'stats' => $stats,
            'form' => $form,  
            'seancesres'=>$seancesres,
        ]);
        }

    }
    #[Route('/res', name: 'app_reservationback', methods: ['GET'])]
    public function index(Request $request,ReservationRepository $reservationRepository): Response
    {
        $reservations = $reservationRepository->createQueryBuilder('r')
        ->leftJoin('r.ids', 's') // Utilisez le nom de la propriété dans l'entité Reservation, ici $ids
        ->getQuery()
        ->getResult();
        $orderBy = $request->query->get('orderBy', 'ASC');
        $sortResult = $reservationRepository->findAllOrderedBySessionDay($orderBy);
        $sessionStatistics = $reservationRepository->countReservationsBySessionName();

        return $this->render('reservation/reservationback.html.twig', [
            'reservations' => $reservations,
            'sortResult' => $sortResult,
            'sessionStatistics' => $sessionStatistics,


        ]);
    }
    #[Route('/dashbord', name: 'app_dashbordb')]
    public function accueil(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('back.html.twig');
    }

    #[Route('/eqb', name: 'app_equipementb')]
    public function equipement(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('equipement/equipementback.html.twig');
    }
    #[Route('/abb', name: 'app_abonnementb')]
    public function abonnement(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('abonnement/abonnementback.html.twig');
    }
    #[Route('/alimentaireb', name: 'app_alimentaireb')]
    public function alimentaire(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('plat/alimentaireback.html.twig');
    }
    #[Route('/eveb', name: 'app_evenementb')]
    public function evenement(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('evenement/evenementback.html.twig');
    }
    #[Route('/recb', name: 'app_reclamationb')]
    public function reclamation(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('reclamation/reclamationback.html.twig');
    }
}

