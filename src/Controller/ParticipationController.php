<?php

namespace App\Controller;

use App\Entity\Evenement;
use App\Entity\Participation;
use App\Form\ParticipationType;
use App\Repository\ParticipationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


#[Route('/participation')]
class ParticipationController extends AbstractController
{

    #[Route('/user-images/{imageName}', name: 'user_images')]
    public function getUserImage(string $imageName): Response
    {
        $imagePath = 'C:\xampp\htdocs\imageProjet\\' . $imageName;
        
        // Return the image as a response
        return new BinaryFileResponse($imagePath);
    }
    ////////affichage////////


    #[Route('/', name: 'app_participation_index', methods: ['GET'])]
    public function index(ParticipationRepository $participationRepository, Request $request): Response
    {
        $searchTerm = $request->query->get('search');
    
        if ($searchTerm) {
            $participations = $participationRepository->search($searchTerm);
        } else {
            $participations = $participationRepository->findAll();
        }
        


        // Récupérer les statistiques
    $statistics = $participationRepository->getParticipantsCountByEvent();

    return $this->render('participation/index.html.twig', [
        'participations' => $participations,
        'statistics' => $statistics, // Passer les statistiques à la vue
    ]);
       
    }

    //////////////////recherche///////////////

    #[Route('/search', name: 'app_participation_search', methods: ['GET'])]
public function search(Request $request, ParticipationRepository $participationRepository): Response
{
    $searchTerm = $request->query->get('search');

    if ($searchTerm) {
        $participations = $participationRepository->search($searchTerm);
        $statistics = $participationRepository->getParticipantsCountByEvent();
    } else {
        $participations = [];
        $statistics = [];
    }

    return $this->render('participation/index.html.twig', [
        'participations' => $participations,
        'statistics' => $statistics,
    ]);
}


/*
#[Route('/addparticipation', name: 'app_participation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, ParticipationRepository $participationRepository,int $userId, UserRepository $userRepository, Security $security): Response
{
    // Récupérer l'ID de l'événement à partir de la requête
    $idEve = $request->query->get('idEve');

    // Si l'ID de l'événement n'est pas récupéré, rediriger vers une autre page ou afficher un message d'erreur
    if (!$idEve) {
        // Rediriger vers une autre page ou afficher un message d'erreur
    }

    // Charger l'entité Evenement correspondante à partir de la base de données
    $evenement = $entityManager->getRepository(Evenement::class)->find($idEve);

    // Si l'événement n'est pas trouvé, rediriger vers une autre page ou afficher un message d'erreur
    if (!$evenement) {
        // Rediriger vers une autre page ou afficher un message d'erreur
    }

    // Vérifier si le nombre maximum de participants est atteint
    if ($evenement->getNbrMax() !== null && count($participationRepository->findBy(['idf_event' => $evenement->getIdEve()])) >= $evenement->getNbrMax()) {
        // Rediriger vers la page de fermeture de l'événement avec l'image
        return $this->redirectToRoute('app_evenement_close');
    }

    // Créer une nouvelle instance de Participation
    $participation = new Participation();

    // Créer le formulaire pour la participation
    $form = $this->createForm(ParticipationType::class, $participation);

    // Gérer la soumission du formulaire
    $form->handleRequest($request);

    // Vérifier si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Définir l'événement dans la participation
        $participation->setIdfEvent($evenement);

        // Persister la nouvelle participation
        $entityManager->persist($participation);
        // Appliquer les changements à la base de données
        $entityManager->flush();

        // Rediriger vers une autre page après la création de la participation
        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }

    // Afficher à nouveau le formulaire de création de participation
    return $this->renderForm('participation/new.html.twig', [
        'participation' => $participation,
        'form' => $form,
    ]);
}

*/

#[Route('/addparticipation', name: 'app_participation_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, ParticipationRepository $participationRepository, UserRepository $userRepository, Security $security): Response
{
    // Récupérer l'utilisateur actuel
    $currentUser = $security->getUser();
    
    // Vérifier si un utilisateur est connecté
    if (!$currentUser) {
        // Gérer le cas où aucun utilisateur n'est connecté
        // Redirection, message d'erreur, etc.
    }
    
    // Récupérer l'ID de l'événement à partir de la requête
    $idEve = $request->query->get('idEve');

    // Si l'ID de l'événement n'est pas récupéré, rediriger vers une autre page ou afficher un message d'erreur
    if (!$idEve) {
        // Rediriger vers une autre page ou afficher un message d'erreur
    }

    // Charger l'entité Evenement correspondante à partir de la base de données
    $evenement = $entityManager->getRepository(Evenement::class)->find($idEve);

    // Si l'événement n'est pas trouvé, rediriger vers une autre page ou afficher un message d'erreur
    if (!$evenement) {
        // Rediriger vers une autre page ou afficher un message d'erreur
    }

    // Vérifier si le nombre maximum de participants est atteint
    if ($evenement->getNbrMax() !== null && count($participationRepository->findBy(['idf_event' => $evenement->getIdEve()])) >= $evenement->getNbrMax()) {
        // Rediriger vers la page de fermeture de l'événement avec l'image
        return $this->redirectToRoute('app_evenement_close');
    }

    // Vérifier si l'utilisateur est déjà inscrit à un événement qui commence à la même date
    $existingParticipation = $participationRepository->createQueryBuilder('p')
        ->join('p.idf_event', 'e')
        ->where('p.id_User = :user')
        ->andWhere('e.dateDeve = :dateDeve')
        ->setParameter('user', $currentUser)
        ->setParameter('dateDeve', $evenement->getDateDeve())
        ->getQuery()
        ->getResult();

        if (count($existingParticipation) > 0) {
            // Instead of throwing an exception, display a user-friendly message
            $this->addFlash('warning', 'Vous êtes déjà inscrit à un événement qui commence à la même date.');
            // Rediriger vers une autre page ou afficher un message d'erreur
            return $this->redirectToRoute('app_evenement_index');
        }
    // Créer une nouvelle instance de Participation
    $participation = new Participation();
    
    // Récupérer le nom et le prénom de l'utilisateur connecté
    $userFirstName = $currentUser->getNom();
    $userLastName = $currentUser->getPrenom();
    
    // Assigner le nom et le prénom de l'utilisateur connecté à la participation
    $participation->setNomP($userFirstName);
    $participation->setPrenomP($userLastName);

    // Assigner l'utilisateur actuel à la participation
    $participation->setIdUser($currentUser);

    // Créer le formulaire pour la participation
    $form = $this->createForm(ParticipationType::class, $participation);

    // Gérer la soumission du formulaire
    $form->handleRequest($request);

    // Vérifier si le formulaire est soumis et valide
    if ($form->isSubmitted() && $form->isValid()) {
        // Définir l'événement dans la participation
        $participation->setIdfEvent($evenement);

        // Persister la nouvelle participation
        $entityManager->persist($participation);
        // Appliquer les changements à la base de données
        $entityManager->flush();

        // Rediriger vers une autre page après la création de la participation
        return $this->redirectToRoute('app_evenement_index', [], Response::HTTP_SEE_OTHER);
    }

    // Afficher à nouveau le formulaire de création de participation
    return $this->renderForm('participation/new.html.twig', [
        'participation' => $participation,
        'form' => $form,
    ]);
}

    

    #[Route('/{idP}', name: 'app_participation_show', methods: ['GET', 'POST'])]
    public function show(Participation $participation): Response
    {
        return $this->render('participation/show.html.twig', [
            'participation' => $participation,
        ]);
    }

    #[Route('/{idP}/edit', name: 'app_participation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParticipationType::class, $participation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('participation/edit.html.twig', [
            'participation' => $participation,
            'form' => $form,
        ]);
    }

    #[Route('/{idP}', name: 'app_participation_delete', methods: ['POST'])]
    public function delete(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$participation->getIdP(), $request->request->get('_token'))) {
            $entityManager->remove($participation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_participation_index', [], Response::HTTP_SEE_OTHER);
    }




    //////trie/////////

    #[Route('/close', name: 'app_participation_close', methods: ['POST'])]
    public function close(Request $request, Participation $participation, EntityManagerInterface $entityManager): Response
    {
       

        return $this->renderForm('participation/closeevent.html.twig');
    }

  

}
