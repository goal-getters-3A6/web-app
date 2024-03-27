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

#[Route('/eq')]
class EquipementController extends AbstractController
{
    #[Route('/', name: 'app_equipement_index', methods: ['GET'])]
    public function index(EquipementRepository $equipementRepository): Response
    {
        return $this->render('equipement/equipement.html.twig', [
            'equipements' => $equipementRepository->findAll(),
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

    #[Route('/{idEq}/avis', name: 'avis_equipement')]
    public function avisEquipement(Equipement $equipement, EntityManagerInterface $entityManager, Request $request): Response
    {
        $avisEquipements = $entityManager->getRepository(Avisequipement::class)->findBy(['idEq' => $equipement]);
    
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

    // Redirection vers la même page après la suppression de l'avis
    return $this->redirectToRoute('avis_equipement', ['idEq' => $avisequipement->getIdEq()->getIdEq()]);
}

}
