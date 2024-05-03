<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;

use Symfony\Component\Mailer\MailerInterface;

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
#[Route('/reservation')]
class ReservationController extends AbstractController
{    
    #[Route('/', name: 'app_reservation_index', methods: ['GET'])]
    public function index(Request $request,ReservationRepository $reservationRepository): Response
    {   $userName = $request->query->get('searchBy') === 'userName' ? $request->query->get('searchTerm') : null;
        $sessionName = $request->query->get('searchBy') === 'sessionName' ? $request->query->get('searchTerm') : null;
        $sessionDay = $request->query->get('searchBy') === 'sessionDay' ? $request->query->get('searchTerm') : null;
        $reservations = [];
    
        if ($userName || $sessionName || $sessionDay) {
            $reservations = $reservationRepository->searchReservations($userName, $sessionName, $sessionDay);
        }
        $orderBy = $request->query->get('orderBy', 'ASC');
        $sortResult = $reservationRepository->findAllOrderedBySessionDay($orderBy);
        $sessionStatistics = $reservationRepository->countReservationsBySessionName();

        return $this->render('reservation/reservationback.html.twig', [
            'reservations' => $reservations,
            'sortResult' => $sortResult,
            'sessionStatistics' => $sessionStatistics,

        ]);
    }
    
    #[Route('/new', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,int $userId, UserRepository $userRepository, Security $security): Response
    {    // Récupérer l'utilisateur actuel
        $currentUser = $security->getUser(); //trajaali 30
         $user=new User();
         if ($currentUser)
         {
            $user = $userRepository->find($userId);
            if ($user) {
                $entityManager->refresh($user);
                $reservation = new Reservation();
                $form = $this->createForm(ReservationType::class, $reservation);
                $form->handleRequest($request);
        
                if ($form->isSubmitted() && $form->isValid()) {
                    $entityManager->persist($reservation);
                    $entityManager->flush();
                   
        
                    return $this->redirectToRoute('app_reservation_new', [], Response::HTTP_SEE_OTHER);
                }
        
                return $this->renderForm('reservation/new.html.twig', [
                    'reservation' => $reservation,
                    'form' => $form,
                    'user' => $user,
                ]);
            }
            else {
                // Gérer le cas où l'utilisateur n'existe pas
                // Retourner une réponse appropriée, par exemple une erreur 404
            }
         }
         else {
            // Gérer le cas où aucun utilisateur n'est connecté
            // Redirection, message d'erreur, etc.
        }
     
    }

    #[Route('/{idreservation}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    #[Route('/{idreservation}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

   /* #[Route('/{idreservation}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager, MailerInterface $mailer): Response*/
    //{
       /* if ($this->isCsrfTokenValid('delete'.$reservation->getIdreservation(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);*/
        /*if ($this->isCsrfTokenValid('delete'.$reservation->getIdreservation(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
    
            // Créer une nouvelle instance de PHPMailer
            $mail = new PHPMailer(true);
    
            try {
                // Paramètres du serveur
                $mail->SMTPDebug = 2;                                 
                $mail->isSMTP();                                      
                $mail->Host = 'smtp.office365.com';  
                $mail->SMTPAuth = true;                               
                $mail->Username = 'Sarra Hammemi';                 
                $mail->Password = '211JFt436114515103';                           
                $mail->SMTPSecure = 'tls';                            
                $mail->Port = 587;                                    
    
                // Destinataires
                $mail->setFrom('sarrahammemi524@gmail.com', 'Mailer');
                $mail->addAddress('sarra.hammemi@esprit.tn', 'Joe User');     
    
                // Contenu
                $mail->isHTML(true);                                  
                $mail->Subject = 'Annulation de la réservation';
                $mail->Body    = 'Votre réservation a été annulée.';
    
                $mail->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
            }
        }
    
        return $this->redirectToRoute('app_reservation_index');*/

       /* if ($this->isCsrfTokenValid('delete'.$reservation->getIdreservation(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
    
            // Créer l'email
            $email = (new \Symfony\Component\Mime\Email())
            ->from('sarrahammemi524@gmail.com')
            ->to('sarra.hammemi@esprit.tn')
            ->subject('Annulation de la réservation')
            ->text('Votre réservation a été annulée.');
        
    
            // Envoyer l'email
            $mailer->send($email);
        }
    
        return $this->redirectToRoute('app_reservation_index');*/
        
   // }
   #[Route('/{idreservation}', name: 'app_reservation_delete', methods: ['POST'])]
   public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
   {
       if ($this->isCsrfTokenValid('delete'.$reservation->getIdreservation(), $request->request->get('_token'))) {
           $entityManager->remove($reservation);
           $entityManager->flush();
   
           // Créer une nouvelle instance de PHPMailer
           $mail = new PHPMailer(true);
           $mail->SMTPDebug = 2;

   
           try {
               // Paramètres du serveur
              $mail->isSMTP();

              $mail->Host = 'smtp-mail.outlook.com';
              $mail->SMTPAuth = true;
               $mail->Username = 'sarra.hammemi@esprit.tn';
               $mail->Password = '211JFt4361';
               $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
               $mail->Port = 587;
   
               // Destinataires
               $mail->setFrom('sarra.hammemi@esprit.tn', 'Service de reservation');
               $mail->addAddress('jbooo7345@gmail.com');
   
               // Contenu
               $mail->isHTML(true);
               $mail->Subject = 'Annulation de la reservation pour ' . $reservation->getIduser()->getNom();
               $mail->Body    = 'Cher(e) ' . $reservation->getIduser()->getNom() 
               . ',<br><br>Nous sommes désolés de vous informer que votre réservation pour la séance de ' . 
               $reservation->getIds()->getNom() . ' a été annulée en raison de circonstances imprévues.
               <br><br>Nous nous excusons pour tout inconvénient que cela pourrait causer et nous 
               vous remercions de votre compréhension.<br><br>Cordialement,<br>Service de réservation';   
               // Envoyer l'email
               $mail->send();
           } catch (Exception $e) {
               echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
           }
           
       }
   
       return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
   }
    #[Route('/search', name: 'reservation_search', methods: ['GET'])]
    public function search(Request $request, ReservationRepository $reservationRepository): Response
    {
      

        return $this->render('reservation/reservationback.html.twig', [
        ]);
    }
    #[Route("/cluster-reservations", name: "cluster_reservations")]
    public function clusterReservations(SerializerInterface $serializer, ReservationRepository $reservationRepository): Response
    {
        // Récupérer toutes les réservations de séances
        $reservations = $reservationRepository->findAll();

        // Sérialiser les données de réservation
        $reservationsJson = $serializer->serialize($reservations, 'json');

        // Définir le chemin du script Python avec les arguments
        $pythonScriptPath = '../python/clustering.py'; // À mettre à jour avec le chemin réel de votre script Python
        $command = ['python3', $pythonScriptPath, $reservationsJson]; // Modifiez pour Python 2.x si nécessaire

        // Créer un nouveau processus
        $process = new Process($command);

        // Exécuter le script Python
        $process->run();

        // Vérifier si l'exécution a réussi
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Récupérer la sortie du script Python
        $output = $process->getOutput();

        // Traiter la sortie selon les besoins
        // (par exemple, décoder JSON en tableau PHP)

        // Passer les données traitées à une autre route Symfony ou à une vue

        // Redirection vers une autre route Symfony avec les données traitées
        return $this->redirectToRoute('app_reservation_index', [
            // Passer les données traitées à une autre route Symfony
        ]);
    }
}
