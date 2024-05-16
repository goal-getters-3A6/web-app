<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\Seance;
use App\Form\ReservationType;
use App\Entity\User;
use App\Repository\UserRepository;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Form\SeanceType;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use mikehaertl\wkhtmlto\Pdf;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Security\Core\Security;

class TemplatesController extends AbstractController
{
   /* #[Route('/r', name: 'app_reservation',methods: ['GET', 'POST'])]
    public function reservation(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($reservation);
            $entityManager->flush();
             // Lecture du message de succès avec ResponsiveVoice
             $successMessage = 'Votre réservation a été ajoutée avec succès.';
            $this->readTextWithResponsiveVoice($successMessage);
            $this->addFlash('success', $successMessage);
            
        // Stocker le message dans une variable de session pour qu'il soit accessible côté client
        $request->getSession()->set('success_message', $successMessage);
          // Vérifier si le client veut générer un PDF
          $generatePdf = $request->request->get('generate_pdf');

         if ($generatePdf) {
        // Générer le contenu HTML du PDF
       /* $pdfHtml = $this->renderView('reservation/pdf_template.html.twig', [
            'reservation' => $reservation,
        ]);*/
         // Stocker le lien vers le PDF généré dans une variable de session
      //   $pdfHtml = $this->renderView('reservation/pdf_template.html.twig', [
          //  'reservation' => $reservation,
      //  ]);
       
        // Créer une nouvelle instance de Pdf
     //  $pdf = new Pdf();

        // Ajouter une page au PDF à partir du contenu HTML
       // $pdf->addPage($pdfHtml);

        // Définir le chemin de sauvegarde du PDF
       // $pdfPath = 'public/pdf.pdf';

        // Sauvegarder le PDF
       // $pdf->saveAs($pdfPath);

        // Stocker le lien vers le PDF généré dans une variable de session
       // $request->getSession()->set('pdf_link', $pdfPath);
          // Stocker le lien vers le PDF généré dans une variable de session
        //  $pdfLink = 'reservation/pdf_template.html.twig';
         // $request->getSession()->set('pdf_link', $pdfLink);

        // Générer le PDF
       /* $pdf = new Pdf();
        $pdf->addPage($pdfHtml);*/ // Utilisez la méthode addPage() pour ajouter une page à partir du contenu HTML

        // Envoi du PDF au navigateur
        //$pdf->send('reservation.pdf');
      //  }          
         //   return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
            


       // }
     /*   $allReservations = $entityManager->getRepository(Seance::class)->findAll();

        return $this->renderForm('reservation/reservation.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
            'allReservations' => $allReservations, // Passer toutes les réservations à la vue
            'pdfLink' => $request->getSession()->get('pdf_link'), // Inclure le lien vers le PDF généré
            'successMessage' => $request->getSession()->get('success_message'), // Passer le message de succès au template


        ]);
    }*/
  /*  #[Route('/r', name: 'app_reservation',methods: ['GET', 'POST'])]
    public function reservation(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) 
        {
            $entityManager->persist($reservation);
            $entityManager->flush();
             // Lecture du message de succès avec ResponsiveVoice
             $successMessage = 'Votre réservation a été ajoutée avec succès.';
            $this->readTextWithResponsiveVoice($successMessage);
            $this->addFlash('success', $successMessage);
            
        // Stocker le message dans une variable de session pour qu'il soit accessible côté client
        $request->getSession()->set('success_message', $successMessage);
           // Récupérer l'ID de l'utilisateur associé à la réservation
        $userId = $reservation->getIdUser();

        // Récupérer l'objet User correspondant à partir de l'ID
        $user = $entityManager->getRepository(User::class)->find($userId);

        // Récupérer l'ID de la séance associée à la réservation
        $seanceId = $reservation->getIds();

        // Récupérer l'objet Seance correspondant à partir de l'ID
        $seance = $entityManager->getRepository(Seance::class)->find($seanceId);

        // Vérifier si le client veut générer un PDF
        $generatePdf = $request->request->get('generate_pdf');


           if ($generatePdf && $seance && $user)
            {
                 // Récupérer les informations nécessaires pour générer le PDF
                 $reservationData = [
                    'nom' => $user->getNom(),
                    'prenom' => $user->getPrenom(),
                    'nomSeance' => $seance->getNom(),
                    'jour' => $seance->getJour(),
                    'horaire' => $seance->getHoraire(),
                ];
                            // Créer une instance de Dompdf et générer le PDF...
                             // Créer une instance de Dompdf
                     $dompdf = new Dompdf();

                   // Rendre le contenu HTML à partir du fichier Twig
                   $html = $this->renderView('reservation/pdf_template.html.twig', [
                    'reservationData' => $reservationData,
                 ]);
 
          // Charger le contenu HTML dans Dompdf
           $dompdf->loadHtml($html);

           // Rendre le PDF
         $dompdf->render();

          // Enregistrer le PDF sur le serveur
         $pdfFileName = 'reservation.pdf';
         $pdfPath = $this->getParameter('kernel.project_dir') . '/public/' . $pdfFileName;
         file_put_contents($pdfPath, $dompdf->output());
    
           // Stocker le lien vers le PDF généré dans une variable de session
            $request->getSession()->set('pdf_link', '/'.$pdfFileName);

      }          
            return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
            


          }
        $allReservations = $entityManager->getRepository(Seance::class)->findAll();

        return $this->renderForm('reservation/reservation.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
            'allReservations' => $allReservations, // Passer toutes les réservations à la vue
            'pdfLink' => $request->getSession()->get('pdf_link'), // Inclure le lien vers le PDF généré
            'successMessage' => $request->getSession()->get('success_message'), // Passer le message de succès au template


        ]);
   
 }*/
 function imageToBase64($imagePath) {
    $type = pathinfo($imagePath, PATHINFO_EXTENSION);
    $data = file_get_contents($imagePath);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    return $base64;
}
 /*#[Route('/r', name: 'app_reservation',methods: ['GET', 'POST'])]
public function reservation(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
{
    $reservation = new Reservation();
    $form = $this->createForm(ReservationType::class, $reservation);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) 
    {
        
        $entityManager->persist($reservation);
        $entityManager->flush();
        // Lecture du message de succès avec ResponsiveVoice
        $successMessage = 'Votre réservation a été ajoutée avec succès.';
        $this->readTextWithResponsiveVoice($successMessage);
        $this->addFlash('success', $successMessage);
        
        // Stocker le message dans une variable de session pour qu'il soit accessible côté client
        $request->getSession()->set('success_message', $successMessage);
        // Récupérer l'ID de l'utilisateur associé à la réservation
        $userId = $reservation->getIdUser();

        // Récupérer l'objet User correspondant à partir de l'ID
        $user = $entityManager->getRepository(User::class)->find($userId);

        // Récupérer l'ID de la séance associée à la réservation
        $seanceId = $reservation->getIds();

        // Récupérer l'objet Seance correspondant à partir de l'ID
        $seance = $entityManager->getRepository(Seance::class)->find($seanceId);

        // Récupérer les informations nécessaires pour générer le PDF
        $reservationData = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'nomSeance' => $seance->getNom(),
            'jour' => $seance->getJourseance(),
            'horaire' => $seance->getHoraire(),
        ];
        // Créer une instance de Dompdf et générer le PDF...
        // Créer une instance de Dompdf
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/Front/images/pdfbg.jpg';
        $imageBase64 = $this->imageToBase64($imagePath);
        $dompdf = new Dompdf();

        // Rendre le contenu HTML à partir du fichier Twig
        $html = $this->renderView('reservation/pdf_template.html.twig', [
            'reservationData' => $reservationData,
            'imageBase64' => $imageBase64,


        ]);

        // Charger le contenu HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Rendre le PDF
        $dompdf->render();

        // Enregistrer le PDF sur le serveur
        $pdfFileName = 'reservation.pdf';
        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/' . $pdfFileName;
        file_put_contents($pdfPath, $dompdf->output());

        // Stocker le lien vers le PDF généré dans une variable de session
        $request->getSession()->set('pdf_link', '/'.$pdfFileName);
                  
        return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
    }
    $allReservations = $entityManager->getRepository(Seance::class)->findAll();

    return $this->renderForm('reservation/reservation.html.twig', [
        'reservation' => $reservation,
        'form' => $form,
        'allReservations' => $allReservations, // Passer toutes les réservations à la vue
        'pdfLink' => $request->getSession()->get('pdf_link'), // Inclure le lien vers le PDF généré
        'successMessage' => $request->getSession()->get('success_message'), // Passer le message de succès au template
    ]);
}
*/
const NBMAXIMALE = 5;

#[Route('/r', name: 'app_reservation',methods: ['GET', 'POST'])]
public function reservation(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger,Security $security): Response
{
    // Définir une constante pour le nombre maximum de réservations

    $reservation = new Reservation();
    //$userId = 35;
    $userId=$security->getUser();
    $form = $this->createForm(ReservationType::class, $reservation);
   
    $user = $entityManager->getRepository(User::class)->find($userId);
    $reservation->setIduser($user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) 
    {
        // Récupérer l'ID de la séance associée à la réservation
        $seanceId = $reservation->getIds();

        // Récupérer le nombre actuel de réservations pour cette séance
        $currentReservations = $entityManager->getRepository(Reservation::class)->getReservationCountForSeance($seanceId);

        // Vérifier si le nombre actuel de réservations est inférieur au maximum autorisé
        if ($currentReservations >= self::NBMAXIMALE) {
            // Afficher un message d'erreur
            $this->addFlash('error', 'Désolé, cette séance est complète.Vous pouvez choisir une autre seance.');
            return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
        }
       
        // Continuer avec la réservation...
        $entityManager->persist($reservation);
        $entityManager->flush();

        // Lecture du message de succès avec ResponsiveVoice
        $successMessage = 'Votre réservation a été ajoutée avec succès.';
        $this->readTextWithResponsiveVoice($successMessage);
        $this->addFlash('success', $successMessage);
        
        // Stocker le message dans une variable de session pour qu'il soit accessible côté client
        $request->getSession()->set('success_message', $successMessage);

        // Récupérer l'ID de l'utilisateur associé à la réservation
     //   $userId = $reservation->getIdUser();
         
        // Récupérer l'objet User correspondant à partir de l'ID
        $seance = $entityManager->getRepository(Seance::class)->find($seanceId);

        // Récupérer les informations nécessaires pour générer le PDF
        $reservationData = [
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'nomSeance' => $seance->getNom(),
            'jour' => $seance->getJourseance(),
            'horaire' => $seance->getHoraire(),
        ];

       
        // Créer une instance de Dompdf
        $imagePath = $this->getParameter('kernel.project_dir') . '/public/Front/images/pdfbg.jpg';
        $imageBase64 = $this->imageToBase64($imagePath);
        $dompdf = new Dompdf();

        // Rendre le contenu HTML à partir du fichier Twig
        $html = $this->renderView('reservation/pdf_template.html.twig', [
            'reservationData' => $reservationData,
            'imageBase64' => $imageBase64,
        ]);

        // Charger le contenu HTML dans Dompdf
        $dompdf->loadHtml($html);

        // Rendre le PDF
        $dompdf->render();

        // Enregistrer le PDF sur le serveur
        $pdfFileName = 'reservation.pdf';
        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/' . $pdfFileName;
        file_put_contents($pdfPath, $dompdf->output());

        // Stocker le lien vers le PDF généré dans une variable de session
        $request->getSession()->set('pdf_link', '/'.$pdfFileName);
                  
        return $this->redirectToRoute('app_reservation', [], Response::HTTP_SEE_OTHER);
    }

    $allReservations = $entityManager->getRepository(Seance::class)->findAll();

    return $this->renderForm('reservation/reservation.html.twig', [
        'reservation' => $reservation,
        'form' => $form,
        'allReservations' => $allReservations, // Passer toutes les réservations à la vue
        'pdfLink' => $request->getSession()->get('pdf_link'), // Inclure le lien vers le PDF généré
        'successMessage' => $request->getSession()->get('success_message'), // Passer le message de succès au template
        'userId' => $userId
    ]);
}
    private function readTextWithResponsiveVoice(string $text): void
    {
        echo '<script>responsiveVoice.speak("' . $text . '", "French Female", {volume: 1});</script>';
    }
    #[Route('/accueil', name: 'app_accueil')]
    public function accueil(): Response
    {  return $this->render('base.html.twig');
  
    }
    #[Route('/apropos', name: 'app_apropos')]
    public function apropos(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('apropos.html.twig');
    }
    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('contact.html.twig');
    }
    #[Route('/eq', name: 'app_equipement')]
    public function equipement(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('equipement/equipement.html.twig');
    }
    #[Route('/ab', name: 'app_abonnement')]
    public function abonnement(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('abonnement/abonnement.html.twig');
    }
    #[Route('/alimentaire', name: 'app_alimentaire')]
    public function alimentaire(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('plat/alimentaire.html.twig');
    }
    #[Route('/eve', name: 'app_evenement')]
    public function evenement(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('evenement/evenement.html.twig');
    }
    #[Route('/rec', name: 'app_reclamation')]
    public function reclamation(): Response
    {
        // Rendu du template reservation.html.twig
        return $this->render('reclamation/reclamation.html.twig');
    }
}

