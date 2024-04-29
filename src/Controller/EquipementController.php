<?php

namespace App\Controller;

use App\Entity\Avisequipement;
use App\Entity\Equipement;
use App\Entity\User;
use App\Form\AvisequipementType;
use App\Repository\EquipementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Security;
use Twilio\Http\GuzzleClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;

require_once ('C:\Users\Yosr\OneDrive - ESPRIT\Bureau\meryem_mayssa_yosr\vendor\twilio\sdk\src\Twilio\autoload.php');


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
            $alertType = $this->handleBadwordAndTentatives($avisequipement , $security); // Récupérez le type d'alerte en fonction de la situation de l'utilisateur
            if ($alertType == null) {
                $avisequipement->setIdEq($equipement); // Associer l'avis à l'équipement
                $avisequipement->setIdUs($user); // Définir l'utilisateur pour l'avis
                $entityManager->persist($avisequipement);
                $entityManager->flush();
                // Redirection vers la même page après l'ajout d'un avis
                return $this->redirectToRoute('avis_equipement', ['idEq' => $equipement->getIdEq()]);
            }
        }
        $emotions = [];
        // Pour chaque avis, détecter l'émotion et stocker l'URL de l'icône d'émotion
        foreach ($avisEquipements as $avis) {
            $emotion = $this->detecterEmotionAction($avis->getCommaeq());
            // Ajouter l'émotion au tableau $emotions
            $emotions[] = $emotion;
            dump($emotions); 
        }
        

        return $this->render('avisequipement/avisequipement.html.twig', [
            'equipement' => $equipement,
            'avisEquipement' => $avisEquipements,
            'form' => $form->createView(),
            'alertType' => $alertType, // Passez la variable alertType au modèle Twig
            'emotions' => $emotions, // Passer les icônes d'émotion au template

        ]);
    } else {
        // Gérer le cas où l'utilisateur n'est pas authentifié, peut-être rediriger vers une page de connexion
        // ou afficher un message d'erreur.
    }
}



private function handleBadwordAndTentatives(Avisequipement $avisequipement,Security $security): ?string // Retourne une chaîne de caractères ou null
{
    // Get the currently logged-in user
    $user = $security->getUser();
    
    if (!$user instanceof User) {
        throw new \LogicException('User must be logged in to perform this action.');
    }

    if ($this->containsBadword($avisequipement->getCommaeq())) {
        // Incrémenter le compteur de tentatives de l'utilisateur
        $user->incrementNbTentative();
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        // Si le nombre de tentatives dépasse 3, modifier le statut de l'utilisateur
        if ($user->getNbTentative() >= 3) {
            $user->setStatut(true);
            $em->flush();

             // Création du client Twilio
             $sid = "AC7cf881ab3739e93930b29f15b5c6d9d8";
             $token = "08f2126a018617aaaa6a73532604e194";
             $twilio = new \Twilio\Rest\Client($sid, $token);
             // Envoi du SMS
             $messageBody = "Bonjour " . $user->getNom() . " " . $user->getPrenom() . ", votre compte a été bloqué.";

             $message = $twilio->messages
                 ->create(
                     "+21697336009", // Numéro de téléphone de destination
                     array(
                        "from" => "+13343928934", // Numéro Twilio
                        "body" => $messageBody // Corps du message
                    )
                );
            return 'block'; // Retourne 'block' pour indiquer que le compte est bloqué
        }

        // Si le compte n'est pas bloqué, enregistrer les modifications et retourner 'warning'
        $em->flush();
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
           

// Vérifier si l'utilisateur actuel est l'auteur de l'avis

    $entityManager->remove($avisequipement);
    $entityManager->flush();

    // Redirection vers la même page après la suppression de l'avis
    return $this->redirectToRoute('avis_equipement', ['idEq' => $avisequipement->getIdEq()->getIdEq()]);
}



#[Route('/{idEq}/like', name: 'equipement_like')]
public function like(Equipement $equipement): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $avisEquipement = new AvisEquipement();
    $avisEquipement->setIdEq($equipement);
    $avisEquipement->setLikes(true);
    $entityManager->persist($avisEquipement);
    $entityManager->flush();

    return $this->redirectToRoute('avis_equipement', ['id' => $equipement->getIdEq()]);
}

#[Route('/equipement/{id}/dislike', name: 'equipement_dislike')]
public function dislike(Equipement $equipement): Response
{
    $entityManager = $this->getDoctrine()->getManager();
    $avisEquipement = new AvisEquipement();
    $avisEquipement->setIdEq($equipement);
    $avisEquipement->setDislikes(true);
    $entityManager->persist($avisEquipement);
    $entityManager->flush();

    return $this->redirectToRoute('avis_equipement', ['id' => $equipement->getIdEq()]);
}



public function detecterEmotionAction($commentaire) {
    try {
        // Créer un client HTTP pour envoyer la requête POST à l'endpoint Flask
        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', 'http://localhost:5000/detect_emotion', [
            'json' => ['comment' => $commentaire],
        ]);

        // Vérifier si la requête a réussi
        if ($response->getStatusCode() === 200) {
            // Obtenir la réponse JSON
            $data = $response->toArray();
            //dd($data);

            // Vérifier si l'émotion a été détectée avec succès
            if (isset($data['emotion_bert'])) {
                // Retourner l'émotion détectée
                return $data['emotion_bert'];
            }
        }
        
        // En cas d'erreur ou de réponse invalide, retourner une valeur par défaut
        return "Non détectée";
    } catch (\Exception $e) {
        // Gérer l'erreur comme vous le souhaitez
        return "Erreur de détection d'émotion";
    }
}



}
