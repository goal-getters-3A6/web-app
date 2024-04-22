<?php

namespace App\Controller;

use App\Entity\Avisequipement;
use App\Entity\Equipement;
use App\Form\AvisequipementType;
use App\Repository\AvisEquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/avisequipement')]
class AvisequipementController extends AbstractController
{
    #[Route('/', name: 'app_avisequipement_index', methods: ['GET'])]
    public function index(AvisEquipementRepository $avisEquipementRepository): Response
    {
        return $this->render('avisequipement/avisequipementback.html.twig', [
            'avisequipements' => $avisEquipementRepository->findAll(),
        ]);
    }

    #[Route('/{idaeq}', name: 'app_avisequipement_show', methods: ['GET'])]
    public function show(Avisequipement $avisequipement): Response
    {
        return $this->render('avisequipement/show.html.twig', [
            'avisequipement' => $avisequipement,
        ]);
    }
    
#[Route('/avisequipement/search', name: 'app_avisequipement_search', methods: ['GET'])]
    public function search(Request $request, AvisEquipementRepository $avisEquipementRepository): Response
    {
        $criteria = array(
            'commaeq' => $request->query->get('commaeq'), // Critère : commaeq
            'idEq' => $request->query->get('idEq'), // Critère : idEq.nomeq
            'idUsNom' => $request->query->get('idUsNom'), // Critère : idUs.nom
            'idUsPrenom' => $request->query->get('idUsPrenom'), // Critère : idUs.prenom
        );

        // Utiliser le repository pour rechercher avec les critères
        $avisequipements = $avisEquipementRepository->findByCriteria($criteria);

        return $this->render('avisequipement/search_result.html.twig', [
            'avisequipements' => $avisequipements,
        ]);
    }




}
