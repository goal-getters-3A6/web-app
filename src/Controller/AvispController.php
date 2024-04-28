<?php

namespace App\Controller;

use App\Entity\Avisp;
use App\Form\AvispType;
use App\Repository\AvispRepository;
use App\Entity\Plat;
use App\Form\PlatType;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/avisp')]
class AvispController extends AbstractController
{
    #[Route('/', name: 'app_avisp_index', methods: ['GET'])]
public function index(AvispRepository $avispRepository): Response
{
    $avisps = $avispRepository->findAll();

    // Initialize counters for ratings
    $ratingCounts = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];

    // Calculate statistics
    foreach ($avisps as $avisp) {
        $star = $avisp->getStar();
        if ($star !== null && $star >= 1 && $star <= 5) {
            $ratingCounts[$star]++;
        }
    }

    // Pass the calculated statistics to the Twig template
    return $this->render('avisp/index.html.twig', [
        'avisps' => $avisps,
        'ratingCounts' => $ratingCounts,
    ]);
}

    
    #[Route('/new/{idp}', name: 'app_avisp_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, $idp): Response
{
    $plat = $entityManager->getRepository(Plat::class)->find($idp); // Fetch the Plat entity by idp

    $avisp = new Avisp();
    $avisp->setIdplat($plat); // Set the Plat entity for the Avisp

    $form = $this->createForm(AvispType::class, $avisp);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $entityManager->persist($avisp);
        $entityManager->flush();

        return $this->redirectToRoute('app_plat_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('avisp/new.html.twig', [
        'avisp' => $avisp,
        'form' => $form,
        'plat' => $plat, // Pass the plat variable to the template
    ]);
}

    #[Route('/{idap}', name: 'app_avisp_show', methods: ['GET'])]
    public function show(Avisp $avisp): Response
    {
        return $this->render('avisp/show.html.twig', [
            'avisp' => $avisp,
        ]);
    }

    #[Route('/{idap}/edit', name: 'app_avisp_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Avisp $avisp, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AvispType::class, $avisp);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_avisp_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avisp/edit.html.twig', [
            'avisp' => $avisp,
            'form' => $form,
        ]);
    }

    #[Route('/{idap}', name: 'app_avisp_delete', methods: ['POST'])]
    public function delete(Request $request, Avisp $avisp, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avisp->getIdap(), $request->request->get('_token'))) {
            $entityManager->remove($avisp);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_avisp_index', [], Response::HTTP_SEE_OTHER);
    }
    /*public function index(AvispRepository $avispRepository): Response
    {
        // Fetching Avisp records for the user with ID 10
        $userAvisps = $avispRepository->findBy(['iduap' => 10]);
    
        return $this->render('avisp/index.html.twig', [
            'avisps' => $userAvisps,
        ]);
    }*/
    #[Route('/uc/{idp}', name: 'app_avisp_indexuc', methods: ['GET'])]
    public function indexuc(AvispRepository $avispRepository, $idp): Response
    {
        $avisps = $avispRepository->findBy(['idplat' => $idp]);
    
        return $this->render('avisp/indexuc.html.twig', [
            'avisps' => $avisps,
        ]);
    }
   
}
