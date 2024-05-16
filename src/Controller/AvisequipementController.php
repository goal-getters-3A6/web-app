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
use Symfony\Component\HttpFoundation\BinaryFileResponse;


#[Route('/avisequipement')]
class AvisequipementController extends AbstractController
{
    #[Route('/user-images/{imageName}', name: 'user_images')]
    public function getUserImage(string $imageName): Response
    {
        $imagePath = 'C:\xampp\htdocs\imageProjet\\' . $imageName;
        
        // Return the image as a response
        return new BinaryFileResponse($imagePath);
    }
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
            'commaeq' => $request->query->get('commaeq'), 
            'idEq' => $request->query->get('idEq'), 
            'idUsNom' => $request->query->get('idUsNom'), 
            'idUsPrenom' => $request->query->get('idUsPrenom'), 
        );

        
        $avisequipements = $avisEquipementRepository->findByCriteria($criteria);

        return $this->render('avisequipement/search_result.html.twig', [
            'avisequipements' => $avisequipements,
        ]);
    }




}
