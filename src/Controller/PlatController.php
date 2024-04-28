<?php

namespace App\Controller;

use App\Entity\Avisp;
use App\Repository\AvispRepository;
use App\Entity\Plat;
use App\Form\AvispType;
use App\Form\PlatType;
use App\Repository\PlatRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use PhpOffice\PhpSpreadsheet\Spreadsheet;




#[Route('/alimentaire')]
class PlatController extends AbstractController
{
    

    #[Route('/', name: 'app_plat_index', methods: ['GET', 'POST'])]
public function index(Request $request, PlatRepository $platRepository): Response
{
    // Create the search form
    $form = $this->createFormBuilder()
        ->add('keyword', TextType::class)
        ->add('search', SubmitType::class, ['label' => 'Search'])
        ->getForm();

    // Handle form submission
    $form->handleRequest($request);

    // If form is submitted and valid, perform search
    if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        $keyword = $data['keyword'];

        // Perform search using PlatRepository
        $plats = $platRepository->search($keyword);
    } else {
        // If form is not submitted or not valid, show all plats
        $plats = $platRepository->findAll();
    }

    // Sorting
    $sortByPrice = $request->query->get('sortByPrice');
    $sortByCalories = $request->query->get('sortByCalories');

    if ($sortByPrice === 'asc') {
        usort($plats, function ($a, $b) {
            return $a->getPrixp() <=> $b->getPrixp();
        });
    } elseif ($sortByPrice === 'desc') {
        usort($plats, function ($a, $b) {
            return $b->getPrixp() <=> $a->getPrixp();
        });
    }

    if ($sortByCalories === 'asc') {
        usort($plats, function ($a, $b) {
            return $a->getCalories() <=> $b->getCalories();
        });
    } elseif ($sortByCalories === 'desc') {
        usort($plats, function ($a, $b) {
            return $b->getCalories() <=> $a->getCalories();
        });
    }

    return $this->render('plat/alimentaire.html.twig', [
        'plats' => $plats,
        'form' => $form->createView(),
    ]);
}


#[Route('/indexb', name: 'app_plat_indexb', methods: ['GET'])]
public function indexb(Request $request, PlatRepository $platRepository): Response
{
    // Check if the 'export' parameter is present in the request query parameters
    if ($request->query->has('export')) {
        // Fetch all plats from the repository
        $plats = $platRepository->findAll();

        // Create a new Spreadsheet instance
        $spreadsheet = new spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Add headers
        $sheet->setCellValue('A1', 'ID')
            ->setCellValue('B1', 'Nom')
            ->setCellValue('C1', 'Prix')
            ->setCellValue('D1', 'Description')
            ->setCellValue('E1', 'Allergies')
            ->setCellValue('F1', 'État')
            ->setCellValue('G1', 'Photo')
            ->setCellValue('H1', 'Calories');

        // Add plat data
        $row = 2;
        foreach ($plats as $plat) {
            $sheet->setCellValue('A'.$row, $plat->getIdp())
                ->setCellValue('B'.$row, $plat->getNomp())
                ->setCellValue('C'.$row, $plat->getPrixp())
                ->setCellValue('D'.$row, $plat->getDescp())
                ->setCellValue('E'.$row, $plat->getAlergiep())
                ->setCellValue('F'.$row, $plat->isEtatp() ? 'Disponible' : 'Non disponible')
                ->setCellValue('G'.$row, $plat->getPhotop())
                ->setCellValue('H'.$row, $plat->getCalories());

            $row++;
        }

        // Create a response object
        $response = new Response();

        // Set headers for the response
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'plats.xlsx'
        );
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $disposition);

        // Write the Spreadsheet to output
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        return $response;
    }

    // If 'export' parameter is not present, proceed with rendering the template
    $plats = $platRepository->findAll(); // Retrieve the plats from the repository

    // Calculate statistics
    $totalPlats = count($plats);
    $totalCalories = array_sum(array_column($plats, 'calories'));
    $averagePrice = $totalPlats > 0 ? array_sum(array_column($plats, 'prixp')) / $totalPlats : 0;

    // Pass the calculated statistics to the template
    return $this->render('plat/indexb.html.twig', [
        'plats' => $plats, // Pass the 'plats' variable to the template
        'totalPlats' => $totalPlats,
        'totalCalories' => $totalCalories,
        'averagePrice' => $averagePrice,
    ]);
}

    #[Route('/new', name: 'app_plat_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $plat = new Plat();
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($plat);
            $entityManager->flush();

            return $this->redirectToRoute('app_plat_indexb', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat/new.html.twig', [
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    #[Route('/{idp}', name: 'app_plat_show', methods: ['GET'])]
    public function show(Plat $plat): Response
    {
        return $this->render('plat/show.html.twig', [
            'plat' => $plat,
            
        ]);

    }
    #[Route('/{idp}/showb', name: 'app_plat_showb', methods: ['GET'])]
    public function showb(Plat $plat): Response
    {
        return $this->render('plat/showb.html.twig', [
            'plat' => $plat,
            
        ]);

    }
    #[Route('/{idp}/edit', name: 'app_plat_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Plat $plat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlatType::class, $plat);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_plat_indexb', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('plat/edit.html.twig', [
            'plat' => $plat,
            'form' => $form,
        ]);
    }

    #[Route('/{idp}', name: 'app_plat_delete', methods: ['POST'])]
    public function delete(Request $request, Plat $plat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plat->getIdp(), $request->request->get('_token'))) {
            $entityManager->remove($plat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_plat_index', [], Response::HTTP_SEE_OTHER);
    }

    
#[Route('/{idp}/avis', name: 'avis_plat')]
public function avisPlat(Plat $plat, EntityManagerInterface $entityManager, Request $request): Response
{
    $avisPlats = $entityManager->getRepository(Avisp::class)->findBy(['idp' => $plat]);
    $avisplat = new Avisp();
    $form = $this->createForm(AvispType::class, $avisplat);
    $form->handleRequest($request);



    if ($form->isSubmitted() && $form->isValid()) {
 

        
            $avisplat->setIdplat($plat); // Associer l'avis à l'équipement
            $entityManager->persist($avisplat);
            $entityManager->flush();
            // Redirection vers la même page après l'ajout d'un avis
            return $this->redirectToRoute('avis_plat', ['idp' => $plat->getIdp()]);
        }
    

    return $this->render('avisp/avisplat.html.twig', [
        'plat' => $plat,
        'avisPlat' => $avisPlats,
        'form' => $form->createView(),
    ]);
}
#[Route('/10/avisf', name: 'avis_platf')]
public function favoritedPlats(UserRepository $userRepository, PlatRepository $platRepository): Response
{
    // Fetch the user by ID
    $user = $userRepository->find(10);
    
    // Fetch favorited plats for the user
    $favoritedPlats = $platRepository->findFavoritedPlatsForUser($user);

    return $this->render('plat/favorited_plats.html.twig', [
        'user' => $user,
        'favoritedPlats' => $favoritedPlats,
    ]);
}

/*#[Route('/export', name: 'app_plat_export', methods: ['GET'])]
public function exportPlats(PlatRepository $platRepository): Response
{
    // Fetch all plats from the repository
    $plats = $platRepository->findAll();

    // Create a new Spreadsheet instance
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Add headers
    $sheet->setCellValue('A1', 'ID')
          ->setCellValue('B1', 'Nom')
          ->setCellValue('C1', 'Prix')
          ->setCellValue('D1', 'Description')
          ->setCellValue('E1', 'Allergies')
          ->setCellValue('F1', 'État')
          ->setCellValue('G1', 'Photo')
          ->setCellValue('H1', 'Calories');

    // Add plat data
    $row = 2;
    foreach ($plats as $plat) {
        $sheet->setCellValue('A'.$row, $plat->getIdp())
              ->setCellValue('B'.$row, $plat->getNomp())
              ->setCellValue('C'.$row, $plat->getPrixp())
              ->setCellValue('D'.$row, $plat->getDescp())
              ->setCellValue('E'.$row, $plat->getAlergiep())
              ->setCellValue('F'.$row, $plat->isEtatp() ? 'Disponible' : 'Non disponible')
              ->setCellValue('G'.$row, $plat->getPhotop())
              ->setCellValue('H'.$row, $plat->getCalories());
        
        $row++;
    }

    // Create a response object
    $response = new Response();

    // Set headers for the response
    $disposition = $response->headers->makeDisposition(
        ResponseHeaderBag::DISPOSITION_ATTACHMENT,
        'plats.xlsx'
    );
    $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    $response->headers->set('Content-Disposition', $disposition);

    // Write the Spreadsheet to output
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');

    return $response;
}*/
}

