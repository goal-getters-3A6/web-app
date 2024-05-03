<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;


#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('reclamation/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ,Security $security): Response
    {
        $user = $security->getUser(); // Récupérer l'utilisateur actuel
        if ($user) {
        $reclamation = new Reclamation();
        $reclamation->setIdu($user);
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          //  $piecejointeFile = $form->get('piecejointerec')->getData();

            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }
    }
    #[Route('/{idrec}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('reclamation/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{idrec}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('reclamation/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }
   
#[Route('/{idrec}/editB', name: 'app_reclamation_editB', methods: ['GET', 'POST'])]
public function editB(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
{
    // Récupérer les données envoyées dans la requête
    $etatrec = $request->request->getInt('etatrec', 0); // Utilisez getInt pour obtenir un entier ou 0 si la valeur n'est pas un entier

    // Modifier les attributs de l'entité Reclamation
    $reclamation->setEtatrec($etatrec);
    if ($request->isMethod('POST')) {
    // Enregistrer les modifications dans la base de données
    $entityManager->flush();
    return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    // Rendre le modèle Twig editB.html.twig avec les données de la réclamation   
}
return $this->render('reclamation/editB.html.twig', [
    'reclamation' => $reclamation,
]);
}

    #[Route('/{idrec}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getIdrec(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('{userId}/reclamations', name: 'reclamations_utilisateur')]
    public function recalamtionUtilisateur(int $userId, UserRepository $userRepository, EntityManagerInterface $entityManager, Request $request, Security $security): Response
    {
       
         // Récupérer l'utilisateur actuel
         $currentUser = $security->getUser(); //trajaali 30
         $user=new User();
        // Vérifier si un utilisateur est connecté
        if ($currentUser) {
            // Récupérer l'utilisateur à partir de son ID
            $user = $userRepository->find($userId);//trajaa user 

            // Vérifier si l'utilisateur existe
            if ($user) {
                // Forcer le chargement complet de l'utilisateur
                $entityManager->refresh($user);

                // Vérifier si l'utilisateur actuel correspond à l'utilisateur dans la route
                if ($user->getId()===30) {
                    // Récupérer les abonnements de l'utilisateur
                    $recalamationUtilisateur = $entityManager->getRepository(Reclamation::class)->findBy(['idu' => $user]);
                    // Rendre la vue avec les abonnements de l'utilisateur connecté
                    return $this->render('reclamation/reclamations_utilisateur.html.twig', [
                        'user' => $user,
                        'reclamations' => $recalamationUtilisateur,
                    ]);
                } else {
                    // Gérer le cas où l'utilisateur actuel n'est pas autorisé à accéder aux abonnements de cet utilisateur
                    // Retourner une réponse appropriée, par exemple une erreur 403
                }
            } else {
                // Gérer le cas où l'utilisateur n'existe pas
                // Retourner une réponse appropriée, par exemple une erreur 404
            }
        } else {
            // Gérer le cas où aucun utilisateur n'est connecté
            // Redirection, message d'erreur, etc.
        }
    }
}
