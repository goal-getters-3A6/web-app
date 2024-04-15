<?php

namespace App\Controller;

use App\Entity\Avisequipement;
use App\Entity\Equipement;
use App\Form\AvisequipementType;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client as GuzzleClient;
use Knp\Component\Pager\PaginatorInterface;

require_once ('C:\Users\Yosr\OneDrive - ESPRIT\Bureau\gg\3-workshop-symfony\vendor\twilio\sdk\src\Twilio\autoload.php');


use Twilio\Rest\Client;

#[Route('/eq')]
class EquipementController extends AbstractController
{
   /* #[Route('/', name: 'app_equipement_index', methods: ['GET'])]
    public function index(EquipementRepository $equipementRepository): Response
    {
        return $this->render('equipement/equipement.html.twig', [
            'equipements' => $equipementRepository->findAll(),
        ]);
    }*/

    #[Route('/', name: 'app_equipement_index', methods: ['GET'])]
public function index(EquipementRepository $equipementRepository, PaginatorInterface $paginator, Request $request): Response
{
    // Récupérer tous les équipements non paginés
    $allEquipements = $equipementRepository->findAll();

    // Paginer les résultats
    $equipements = $paginator->paginate(
        $allEquipements, // Requête à paginer
        $request->query->getInt('page', 1), // Numéro de page par défaut
        3 // Nombre d'équipements par page
    );

    return $this->render('equipement/equipement.html.twig', [
        'equipements' => $equipements,
    ]);
}

    #[Route('/new', name: 'app_equipement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $equipement = new Equipement();
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($equipement);
            $entityManager->flush();

            return $this->redirectToRoute('app_equipement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipement/new.html.twig', [
            'equipement' => $equipement,
            'form' => $form,
        ]);
    }

    #[Route('/{idEq}', name: 'app_equipement_show', methods: ['GET'])]
    public function show(Equipement $equipement): Response
    {
        return $this->render('equipement/show.html.twig', [
            'equipement' => $equipement,
        ]);
    }

    #[Route('/{ideq}/edit', name: 'app_equipement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_equipement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipement/edit.html.twig', [
            'equipement' => $equipement,
            'form' => $form,
        ]);
    }

    #[Route('/{ideq}', name: 'app_equipement_delete', methods: ['POST'])]
    public function delete(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipement->getIdeq(), $request->request->get('_token'))) {
            $entityManager->remove($equipement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipement_index', [], Response::HTTP_SEE_OTHER);
    }

   /* #[Route('/{idEq}/avis', name: 'avis_equipement')]
    public function avisEquipement(Equipement $equipement, EntityManagerInterface $entityManager, Request $request): Response
    {
        $avisEquipements = $entityManager->getRepository(Avisequipement::class)->findBy(['idEq' => $equipement]);
        $commaeq = $avisEquipements->getCommaeq();
        $emotion = $this->detecterEmotion($commaeq);
        $avisequipement = new Avisequipement();
        $form = $this->createForm(AvisequipementType::class, $avisequipement);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $avisequipement->setIdEq($equipement); // Associer l'avis à l'équipement
            $entityManager->persist($avisequipement);
            $entityManager->flush();
    
            // Redirection vers la même page après l'ajout d'un avis
            return new RedirectResponse($this->generateUrl('avis_equipement', ['idEq' => $equipement->getIdEq()]));
        }
    
        return $this->render('avisequipement/avisequipement.html.twig', [
            'equipement' => $equipement,
            'avisEquipement' => $avisEquipements,
            'emotion' => $emotion,
            'form' => $form->createView(),
        ]);
    }*/

    #[Route('/{idEq}/avis', name: 'avis_equipement')]
public function avisEquipement(Equipement $equipement, EntityManagerInterface $entityManager, Request $request): Response
{
    // Récupérer tous les avis pour cet équipement
    $avisEquipements = $entityManager->getRepository(Avisequipement::class)->findBy(['idEq' => $equipement]);

    // Initialiser un tableau pour stocker les émotions de chaque avis
    $emotions = [];

    // Pour chaque avis, détecter l'émotion et l'ajouter au tableau des émotions
    foreach ($avisEquipements as $avis) {
        $commentaire = $avis->getCommaeq();
        $emotion = $this->detecterEmotion($commentaire);
        $emotions[] = $emotion;
    }

    // Créer un formulaire pour ajouter un nouvel avis
    $avisequipement = new Avisequipement();
    $form = $this->createForm(AvisequipementType::class, $avisequipement);
    $form->handleRequest($request);

    // Traitement du formulaire lors de sa soumission
    if ($form->isSubmitted() && $form->isValid()) {
        $avisequipement->setIdEq($equipement);
        $entityManager->persist($avisequipement);
        $entityManager->flush();

        // Redirection vers la même page après l'ajout d'un avis
        return $this->redirectToRoute('avis_equipement', ['idEq' => $equipement->getIdEq()]);
    }

    // Rendre le template Twig avec les données nécessaires
    return $this->render('avisequipement/avisequipement.html.twig', [
        'equipement' => $equipement,
        'avisEquipement' => $avisEquipements,
        'emotions' => $emotions,
        'form' => $form->createView(),
    ]);
}

   


    #[Route('/avis/{id}/edit', name: 'avis_edit', methods: ['GET', 'POST'])]
    public function editAvis(Request $request, Avisequipement $avisequipement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvisequipementType::class, $avisequipement);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
    
            // Rediriger vers la page précédente ou une autre page appropriée
            return $this->redirectToRoute('avis_equipement', ['idEq' => $avisequipement->getIdEq()->getIdEq()], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('avisequipement/edit.html.twig', [
            'avisequipement' => $avisequipement,
            'form' => $form->createView(),
        ]);
    }



#[Route('/avis/{id}/delete', name: 'avis_delete')]
public function deleteAvis(Avisequipement $avisequipement, EntityManagerInterface $entityManager): Response
{

    $entityManager->remove($avisequipement);
    $entityManager->flush();

    

   // Création du client Twilio
   $sid = "AC3f9de0017be9564b86cb4664a10df6b1";
   $token = "67c512333f9c1077be2e0fb2263a4373";
   $twilio = new \Twilio\Rest\Client($sid, $token);

   // Envoi du SMS
   $message = $twilio->messages
     ->create("+21697336009", // Numéro de téléphone de destination
       array(
         "from" => "+19123859879", // Numéro Twilio
         "body" => "Bonjour, votre avis a été supprimé avec succès." // Corps du message
       )
     );
    // Redirection vers la même page après la suppression de l'avis
    return $this->redirectToRoute('avis_equipement', ['idEq' => $avisequipement->getIdEq()->getIdEq()]);
}


 // Fonction pour détecter l'émotion à partir du commentaire
 private function detecterEmotion($commentaire)
 {
    
         // Utilisation du client Guzzle pour envoyer des requêtes HTTP
        $httpClient = new GuzzleClient();
            
         // Envoi de la requête POST à l'API Python pour détecter l'émotion
         $response = $httpClient->post('http://localhost:5000/detect_emotion', [
             'json' => ['comment' => $commentaire]
         ]);
 
         // Analyse de la réponse JSON et récupération de l'émotion détectée
         $responseJson = json_decode($response->getBody(), true);
         return $responseJson['emotion_bert'];
      
 }

 // Fonction pour obtenir l'icône smiley en fonction de l'émotion détectée
 private function getSmileyIcon($emotion)
 {
     $imagePath = '';
     switch ($emotion) {
        case 'Joie':
            $imagePath = '/Front/images/joie.png';
            break;
        case 'Tristesse':
            $imagePath = '/Front/images/tristesse.png';
            break;
        case 'Neutre':
            $imagePath = '/Front/images/neutre.png';
            break;
        default:
            $imagePath = '/Front/images/question.png';
            break;
     }
     return $imagePath;
 }

 // Fonction pour afficher l'icône smiley dans la vue Twig
 
 private function renderSmileyIcon($emotion)
 {
     $imagePath = $this->getSmileyIcon($emotion);
     // Chargez l'image dans votre template Twig
     return '<img src="' . $imagePath . '" alt="' . $emotion . '">';
 }

 


}
