<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\User;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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

    #[Route('/new/{type}', name: 'app_abonnement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager ,Security $security): Response
    {  $user = $security->getUser(); // Récupérer l'utilisateur actuel
        if ($user) {
        $type = $request->query->get('type');
        // Vérifiez si le type est défini, sinon définissez une valeur par défaut
       if (!$type) {
            $type = 'Ordinaire'; // Définir une valeur par défaut
        }
        $montant = $this->calculerMontantSelonTypeAbonnement($type);

        $abonnement = new Abonnement();
        $abonnement->setIdu($user);
        $abonnement->setMontantab($montant);
        $abonnement->setTypeab($type);

        $form = $this->createForm(AbonnementType::class, $abonnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($abonnement);
            $entityManager->flush();

            return $this->redirectToRoute('app_abonnement_new', ['type' => $type], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('abonnement/new.html.twig', [
            'abonnement' => $abonnement,
            'form' => $form,
            'type' => $type,
 
        ]);
    }
    }

    private function calculerMontantSelonTypeAbonnement(?string $type): float
    {
        if ($type === null) {
            // Si le type est nul, vous pouvez choisir de renvoyer une erreur ou une valeur par défaut
            throw new \InvalidArgumentException("Le type d'abonnement n'a pas été spécifié.");
        }

        switch ($type) {
            case 'Ordinaire':
                return 50.0; // Montant pour le type d'abonnement 1
            case 'Familiale':
                return 100.0; // Montant pour le type d'abonnement 2
            case 'Premium':
                return 150.0; // Montant pour le type d'abonnement 3
            default:
                // Si le type n'est pas reconnu, vous pouvez choisir de renvoyer une erreur ou une valeur par défaut
                throw new \InvalidArgumentException("Le type d'abonnement n'est pas valide.");
        }
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
                    $abonnementsUtilisateur = $entityManager->getRepository(Abonnement::class)->findBy(['idu' => $user]);
                    // Rendre la vue avec les abonnements de l'utilisateur connecté
                    return $this->render('abonnement/abonnements_utilisateur.html.twig', [
                        'user' => $user,
                        'abonnements' => $abonnementsUtilisateur,
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
  
#[Route('/abonnement/{userId}/editDate', name: 'app_abonnement_editDate', methods: ['GET', 'POST'])]
public function editDate(Request $request, int $userId, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
{
    // Récupérer l'utilisateur correspondant à l'ID
    $user = $userRepository->find($userId);

    // Vérifier si l'utilisateur existe
    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    // Récupérer l'abonnement de l'utilisateur
   // $abonnement = $user->getAbonnement();
   $abonnement = $entityManager->getRepository(Abonnement::class)->findOneBy(['idu' => $user]);

    // Vérifier si l'abonnement existe
    if (!$abonnement) {
        throw $this->createNotFoundException('Abonnement non trouvé');
    }

    // Vérifier si la méthode HTTP est POST (le formulaire a été soumis)
    if ($request->isMethod('POST')) {
        // Récupérer la nouvelle date d'expiration depuis la requête
        $newExpirationDate = new \DateTime($request->request->get('new_expiration_date'));

        // Modifier la date d'expiration de l'abonnement
        $abonnement->setDateexpirationab($newExpirationDate);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        // Rediriger l'utilisateur vers la page des abonnements de l'utilisateur
        return $this->redirectToRoute('abonnements_utilisateur', ['userId' => $userId], Response::HTTP_SEE_OTHER);
    }

    // Afficher le formulaire de modification de la date d'expiration de l'abonnement
    return $this->render('abonnement/editDate.html.twig', [
        'abonnement' => $abonnement,
    ]);
}

}
