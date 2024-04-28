<?php

namespace App\Controller;

use App\Entity\Avisequipement;
use App\Entity\Equipement;
use App\Entity\User;
use App\Form\AvisequipementType;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use GuzzleHttp\Client as GuzzleClient;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;


//require_once ('C:\Users\Yosr\OneDrive - ESPRIT\Bureau\gg\3-workshop-symfony\vendor\twilio\sdk\src\Twilio\autoload.php');


#[Route('/eq')]
class EquipementController extends AbstractController
{
   

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

    

    #[Route('/{idEq}', name: 'app_equipement_show', methods: ['GET'])]
    public function show(Equipement $equipement): Response
    {
        return $this->render('equipement/show.html.twig', [
            'equipement' => $equipement,
        ]);
    }

    


#[Route('/{idEq}/avis', name: 'avis_equipement')]
public function avisEquipement(Equipement $equipement, EntityManagerInterface $entityManager, Request $request, Security $security): Response
{
    $user = $security->getUser(); // Récupérer l'utilisateur actuel

    // Vérifier si l'utilisateur est authentifié
    if ($user) {
        $avisEquipements = $entityManager->getRepository(Avisequipement::class)->findBy(['idEq' => $equipement]);
        $avisequipement = new Avisequipement();
        $form = $this->createForm(AvisequipementType::class, $avisequipement);
        $form->handleRequest($request);

        $alertType = null; // Initialisez la variable alertType à null par défaut

        if ($form->isSubmitted() && $form->isValid()) {
            $alertType = $this->handleBadwordAndTentatives($avisequipement); // Récupérez le type d'alerte en fonction de la situation de l'utilisateur
            if ($alertType == null) {
                $avisequipement->setIdEq($equipement); // Associer l'avis à l'équipement
                $avisequipement->setIdUs($user); // Définir l'utilisateur pour l'avis
                $entityManager->persist($avisequipement);
                $entityManager->flush();
                // Redirection vers la même page après l'ajout d'un avis
                return $this->redirectToRoute('avis_equipement', ['idEq' => $equipement->getIdEq()]);
            }
        }

        return $this->render('avisequipement/avisequipement.html.twig', [
            'equipement' => $equipement,
            'avisEquipement' => $avisEquipements,
            'form' => $form->createView(),
            'alertType' => $alertType, // Passez la variable alertType au modèle Twig
        ]);
    } else {
        // Gérer le cas où l'utilisateur n'est pas authentifié, peut-être rediriger vers une page de connexion
        // ou afficher un message d'erreur.
    }

}

// Méthode pour vérifier si l'avis contient un mot interdit et gérer le nombre de tentatives
private function handleBadwordAndTentatives(Avisequipement $avisequipement): ?string // Retourne une chaîne de caractères ou null
{
    if ($this->containsBadword($avisequipement->getCommaeq())) {
        // Incrémenter le compteur de tentatives de l'utilisateur
        $user = $this->getDoctrine()->getRepository(User::class)->find(19);
        $user->incrementNbTentative();
        $this->getDoctrine()->getManager()->flush();

        // Si le nombre de tentatives dépasse 3, modifier le statut de l'utilisateur
        if ($user->getNbTentative() >= 3) {
            $user->setStatut(true);
            $this->getDoctrine()->getManager()->flush();

            //return 'block'; // Retourne 'block' pour indiquer que le compte est bloqué
        }
        if ($user->isStatut() === true) {
           // Création du client Twilio
   $sid = "AC3f9de0017be9564b86cb4664a10df6b1";
   $token = "67c512333f9c1077be2e0fb2263a4373";
   $twilio = new \Twilio\Rest\Client($sid, $token);

   // Envoi du SMS
   $message = $twilio->messages
     ->create("+21697336009", // Numéro de téléphone de destination
       array(
         "from" => "+19123859879", // Numéro Twilio
         "body" => "Bonjour, block." // Corps du message
       )
     );
            return 'block'; // Retourne 'block' pour indiquer que le compte est bloqué
        }

        $this->getDoctrine()->getManager()->flush();

        return 'warning'; // Retourne 'warning' pour indiquer que le compte est sous avertissement
    }

    return null; // Retourne null si aucun avertissement n'est nécessaire
}
 // Fonction pour vérifier si l'avis contient un mot interdit
 private function containsBadword($avisContent) {
    $badwords = ['badword1', 'badword2', 'badword3']; // Ajoutez vos mots interdits ici

    foreach ($badwords as $badword) {
        if (stripos($avisContent, $badword) !== false) {
            return true;
        }
    }

    return false;
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

    // Redirection vers la même page après la suppression de l'avis
    return $this->redirectToRoute('avis_equipement', ['idEq' => $avisequipement->getIdEq()->getIdEq()]);
}

/*
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
*/
 


}
