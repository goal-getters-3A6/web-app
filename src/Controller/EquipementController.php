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
use Locale;
use Symfony\Component\Security\Core\Security;
use Twilio\Http\GuzzleClient;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


require_once ('C:\Users\Yosr\OneDrive - ESPRIT\Bureau\meryem_mayssa_yosr\vendor\twilio\sdk\src\Twilio\autoload.php');


#[Route('/eq')]
class EquipementController extends AbstractController
{
   
    #[Route('/user-images/{imageName}', name: 'user_images')]
    public function getUserImage(string $imageName): Response
    {
        $imagePath = 'C:\xampp\htdocs\imageProjet\\' . $imageName;
        
        // Return the image as a response
        return new BinaryFileResponse($imagePath);
    }
    #[Route('/', name: 'app_equipement_index', methods: ['GET'])]
    public function index(EquipementRepository $equipementRepository, PaginatorInterface $paginator, Request $request): Response
{
    $allEquipements = $equipementRepository->findAll();

    $equipements = $paginator->paginate(
        $allEquipements, 
        $request->query->getInt('page', 1), 
        3 
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
    $user = $security->getUser(); 
    if ($user) {
        $avisEquipements = $entityManager->getRepository(Avisequipement::class)->findBy(['idEq' => $equipement]);
        $avisequipement = new Avisequipement();
        $form = $this->createForm(AvisequipementType::class, $avisequipement);
        $form->handleRequest($request);

        $alertType = null; 

        if ($form->isSubmitted() && $form->isValid()) {
            $alertType = $this->handleBadwordAndTentatives($avisequipement , $security); 
            if ($alertType == null) {
                $avisequipement->setIdEq($equipement); 
                $avisequipement->setIdUs($user); 
                $entityManager->persist($avisequipement);
                $entityManager->flush();
                
                return $this->redirectToRoute('avis_equipement', ['idEq' => $equipement->getIdEq()]);
            }
        }
        $emotions = [];
        
        foreach ($avisEquipements as $avis) {
            $emotion = $this->detecterEmotionAction($avis->getCommaeq());
           
            $emotions[] = $emotion;
            dump($emotions); 
        }
        

        return $this->render('avisequipement/avisequipement.html.twig', [
            'equipement' => $equipement,
            'avisEquipement' => $avisEquipements,
            'form' => $form->createView(),
            'alertType' => $alertType, 
            'emotions' => $emotions, 

        ]);
    } else {
        
        
    }
}



private function handleBadwordAndTentatives(Avisequipement $avisequipement,Security $security): ?string // Retourne une chaîne de caractères ou null
{
    
    $user = $security->getUser();
    
    if (!$user instanceof User) {
        throw new \LogicException('User must be logged in to perform this action.');
    }

    if ($this->containsBadword($avisequipement->getCommaeq())) {
       
        $user->incrementNbTentative();
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

       
        if ($user->getNbTentative() >= 3) {
            $user->setStatut(true);
            $em->flush();

             
             $sid = "AC7cf881ab3739e93930b29f15b5c6d9d8";
             $token = "97135cbd0c556b3f3618bde6adbfd07e";
             $twilio = new \Twilio\Rest\Client($sid, $token);
            
             $messageBody = "Bonjour " . $user->getNom() . " " . $user->getPrenom() . ", votre compte a été bloqué.";

             $message = $twilio->messages
                 ->create(
                     "+21697336009", 
                     array(
                        "from" => "+13343928934", 
                        "body" => $messageBody 
                    )
                );
            return 'block'; 
        }

       
        $em->flush();
        return 'warning'; 
    }

    return null; 
}

 
 private function containsBadword($avisContent) {
    $badwords = ['badword1', 'badword2', 'badword3']; 

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

    return $this->redirectToRoute('avis_equipement', ['idEq' => $avisequipement->getIdEq()->getIdEq()]);
}







public function detecterEmotionAction($commentaire) {
    try {
        // Créer un client HTTP pour envoyer la requête POST à l'endpoint Flask
        $httpClient = HttpClient::create();
        $response = $httpClient->request('POST', 'http://localhost:5000/detect_emotion', [
            'json' => ['comment' => $commentaire],
        ]);

        
        if ($response->getStatusCode() === 200) {
            
            $data = $response->toArray();
            //dd($data);

           
            if (isset($data['emotion_bert'])) {
                
                return $data['emotion_bert'];
            }
        }
        
        return "Non détectée";
    } catch (\Exception $e) {
        
        return "Erreur de détection d'émotion";
    }
}



}
