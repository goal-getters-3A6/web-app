<?php

namespace App\Controller;

use App\Entity\Abonnement;
use App\Entity\User;
use App\Form\AbonnementType;
use App\Repository\AbonnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    { $type = $request->query->get('type');
    
        // Vérifiez si le type est défini, sinon définissez une valeur par défaut
       if (!$type) {
            $type = 'Ordinaire'; // Définir une valeur par défaut
        }
        $montant = $this->calculerMontantSelonTypeAbonnement($type);

        $abonnement = new Abonnement();
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
    #[Route('/{idab}/editDate', name: 'app_abonnement_editDate', methods: ['GET', 'POST'])]
public function editDate(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
{
    // Récupérer la nouvelle date d'expiration depuis la requête
    $newExpirationDate = new \DateTime($request->request->get('new_expiration_date'));

    // Modifier la date d'expiration de l'abonnement
    $abonnement->setDateexpirationab($newExpirationDate);
    if ($request->isMethod('POST')) {
    // Enregistrer les modifications dans la base de données
    $entityManager->flush();

    return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
    // Rendre le modèle Twig editB.html.twig avec les données de la réclamation   
}
return $this->render('abonnement/editDate.html.twig', [
    'abonnement' => $abonnement,
]);
}

    #[Route('/{idab}', name: 'app_abonnement_delete', methods: ['POST'])]
    public function delete(Request $request, Abonnement $abonnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$abonnement->getIdab(), $request->request->get('_token'))) {
            $entityManager->remove($abonnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_abonnement_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/abonnements/{userId}', name: 'abonnements_utilisateur')]
    public function abonnementsUtilisateur(int $userId, AbonnementRepository $abonnementRepository): Response
    {
        // Récupérer l'utilisateur à partir de son identifiant
        $user = $this->getDoctrine()->getRepository(User::class)->find($userId);

        // Vérifier si l'utilisateur existe
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        // Récupérer les abonnements de l'utilisateur spécifique
        $abonnementsUtilisateur = $abonnementRepository->findBy(['idu' => $userId]);

        // Rendre la vue avec les abonnements de l'utilisateur spécifique
        return $this->render('abonnement/abonnements_utilisateur.html.twig', [
            'user' => $user,
            'abonnements' => $abonnementsUtilisateur,
        ]);
    }
   
}
