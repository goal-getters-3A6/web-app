<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\User;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\PseudoTypes\True_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

use Stripe\Stripe;
use Stripe\Charge;
use Stripe\PaymentIntent;

/*use App\Service\NotificationService;
use App\Message\NotificationMessage;*/

use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/abonnement')]
class AbonnementController extends AbstractController
{
    #[Route('/app_abonnement_index', name: 'app_abonnement_index', methods: ['GET'])]
    public function index(AbonnementRepository $abonnementRepository): Response
    {
        return $this->render('abonnement/index.html.twig', [
            'abonnements' => $abonnementRepository->findAll(),
        ]);
    }
  /*  private $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }*/
    #[Route('/{"type"}/new', name: 'app_abonnement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ,Security $security): Response
    {  $user = $security->getUser(); // Récupérer l'utilisateur actuel
        if ($user) {
            $type = $request->query->get('type');
            $montant = $this->MontantSelonTypeAbonnement($type) ;
            $codePromo = $request->request->get('abonnement')['codepromoab'] ?? null;
            // Vérifier si un code promo a été saisi avant d'appliquer la réduction
            if ($codePromo !== null) {
                // Appliquer la réduction en fonction du code promo
                switch ($codePromo) {
                    case 'GoFit30':
                        $montant *= 0.7; // Appliquer une réduction de 30%
                        break;
                    case 'GoFit10':
                        $montant *= 0.9; // Appliquer une réduction de 10%
                        break;
                    // Ajoutez d'autres cas pour d'autres codes promo si nécessaire
                }
            }
        $abonnement = new Abonnement();
        $abonnement->setIdu($user);
        $abonnement->setMontantab($montant);
        $abonnement->setTypeab($type);
        

        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($abonnement);
            $entityManager->flush();
           // $this->notificationService->sendNotification(30, 'Votre abonnement expire dans 2 jours.');
            return $this->redirectToRoute('app_abonnement_new', ['type' => $type], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonnement/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form,
            'type' => $type,
 
        ]);
        
    }
    }

    private function MontantSelonTypeAbonnement(?string $type): float
    { // Définition des montants initiaux sans réduction
        $montantsSansReduction = [
            'Ordinaire' => 50.0,
            'Familiale' => 100.0,
            'Premium' => 150.0,
        ];
    
        // Vérifier si le type d'abonnement est valide
        if (!array_key_exists($type, $montantsSansReduction)) {
            throw new \InvalidArgumentException("Le type d'abonnement n'est pas valide.");
        }
    
        // Appliquer une réduction spécifique si nécessaire
        switch ($type) {
            case 'Ordinaire':
                $montant = $montantsSansReduction[$type] ;
                break;
            case 'Familiale':
                $montant = $montantsSansReduction[$type];
                break;
            case 'Premium':
                $montant = $montantsSansReduction[$type];
                break;
        }
    
        return $montant;
    }
    #[Route('/{idab}', name: 'app_abonnement_show', methods: ['GET'])]
    public function show(Abonnement $abonnement): Response
    {
        return $this->render('abonnement/show.html.twig', [
            'abonnement' => $abonnement,
        ]);
    }

    #[Route('/{idab}/edit', name: 'app_abonnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonnement/edit.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form,
        ]);
    }
  /*#[Route('/{idab}/editDate', name: 'app_abonnement_editDate', methods: ['GET', 'POST'])]
public function editDate(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
{
    // Récupérer la nouvelle date d'expiration depuis la requête
    $newExpirationDate = new \DateTime($request->request->get('new_expiration_date'));

    // Modifier la date d'expiration de l'abonnement
    $abonnement->setDateexpirationab($newExpirationDate);
    if ($request->isMethod('POST')) {
    // Enregistrer les modifications dans la base de données
    $entityManager->flush();

    return $this->redirectToRoute('abonnements_utilisateur', [], Response::HTTP_SEE_OTHER);
    // Rendre le modèle Twig editB.html.twig avec les données de l'abonnement'  
}
return $this->render('abonnement/editDate.html.twig', [
    'abonnement' => $abonnement,
]);
}
*/
    #[Route('/{idab}', name: 'app_abonnement_delete', methods: ['POST'])]
    public function delete(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnement->getIdab(), $request->request->get('_token'))) {
            $entityManager->remove($abonnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
    }
   
    #[Route('/{userId}/abonnements', name: 'abonnements_utilisateur')]
    public function abonnementsUtilisateur(int $userId, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request, Security $security): Response
    {
       
        $currentUser = $security->getUser(); //trajaali 30
         $user=new User();
        
        if ($currentUser) {
            
            $user = $userRepository->find($userId);//trajaa user 

            if ($user) {
               
                $entityManager->refresh($user);

                if ($user->getId()) {
                    
                    $abonnementsUtilisateur = $entityManager->getRepository(Abonnement::class)->findBy(['idu' => $user]);
                    // Rendre la vue avec les abonnements de l'utilisateur connecté
                    return $this->render('abonnement/abonnements_utilisateur.html.twig', [
                        'user' => $user,
                        'abonnements' => $abonnementsUtilisateur,
                    ]);
                } else {
                    
                }
            } else {
                
            }
        } else {
            
        }
    }
  
#[Route('/abonnement/{userId}/editDate', name: 'app_abonnement_editDate', methods: ['GET', 'POST'])]
public function editDate(Request $request, int $userId, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
{
    
    $user = $userRepository->find($userId);

    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    // Récupérer l'abonnement de l'utilisateur
   $abonnement = $entityManager->getRepository(Abonnement::class)->findOneBy(['idu' => $user]);

    // Vérifier si l'abonnement existe
    if (!$abonnement) {
        throw $this->createNotFoundException('Abonnement non trouvé');
    }

    // Vérifier que le formulaire a été soumis
    if ($request->isMethod('POST')) {
        // Récupérer la nouvelle date d'expiration depuis la requête
        $newExpirationDate = new \DateTime($request->request->get('new_expiration_date'));

        // Modifier la date 
        $abonnement->setDateexpirationab($newExpirationDate);
        $entityManager->flush();

        return $this->redirectToRoute('abonnements_utilisateur', ['userId' => $userId], Response::HTTP_SEE_OTHER);
    }

    // Afficher le formulaire de modification de la date d'expiration de l'abonnement
    return $this->render('abonnement/editDate.html.twig', [
        'abonnement' => $abonnement,
    ]);
}


    #[Route('/new/Ordinaire/process', name: 'process', methods: ['GET', 'POST'])]
    public function processPayment(Request $request): Response
    {

        return $this->render('abonnement/paiement.html.twig', []);
    }
    
    #[Route('/new/Ordinaire/process/payment', name: 'payment', methods: ['POST'])]
    public function payment(Request $request): Response
    {
        $montantEnEuros = 50; 
        $montantEnCentimes = $montantEnEuros * 100; // Convertir en centimes
        try {
            // Configurez la clé secrète de l'API Stripe
            Stripe::setApiKey('sk_test_51KrVQ5IXjPg5Xb6hm4Wrb0yFLMtMZ13tk4JV4znVJYb3xU4f4SMRCUPSeglrlXEZriWFniholkwYvQE2yQ5eGv5800HHlAQCbs');

            // Créez le paiement avec Stripe
            $paymentIntent = PaymentIntent::create([
                'amount' => $montantEnCentimes,
                'currency' => 'usd',
                'description' => 'Paiement pour un abonnement', // Ajoutez la description ici
                //'source' => "4242424242424242",
                'receipt_email' => 'hakimimayssa@gmail.com', // Ajoutez l'email ici
               
            ]);
          // Si le paiement est réussi, redirigez vers la page success.html.twig
          return $this->redirectToRoute('abonnement/paiement.html.twig');

        } catch (\Exception $e) {
            // Vous pouvez également inclure un message d'erreur spécifique si nécessaire
            return $this->render('abonnement/paiement.html.twig', ['error_message' => $e->getMessage()]);
        }
    }
    
   

}
