<?php

namespace App\Controller;

use App\Entity\Seance;
use App\Form\SeanceType;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
#[Route('/seance')]
class SeanceController extends AbstractController
{    
    #[Route('/user-images/{imageName}', name: 'user_images')]
public function getUserImage(string $imageName): Response
{
    $imagePath = 'C:\xampp\htdocs\imageProjet\\' . $imageName;
    
    // Return the image as a response
    return new BinaryFileResponse($imagePath);
}

    #[Route('/', name: 'app_seance_index', methods: ['GET'])]
    public function index(SeanceRepository $seanceRepository): Response
    {
        return $this->render('seance/index.html.twig', [
            'seances' => $seanceRepository->findAll(),
        ]);
    }
    
    
 
    #[Route('/new', name: 'app_seance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SeanceRepository $seanceRepository): Response
    {
       /* $seance = new Seance();
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);
         
        if ($form->isSubmitted() && $form->isValid()) {
            
            $imageFile = $form->get('imageseance')->getData();
            if ($imageFile) {
                // Move uploaded file to desired location
                // Example:
                $uploadsDirectory = $this->getParameter('uploads_directory');
                $imageFileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $uploadsDirectory,
                    $imageFileName
                );
                // Set image file name to user entity
                $seance->setImageseance($imageFileName);
            }
            $entityManager->persist($seance);
            $entityManager->flush();

            return $this->redirectToRoute('app_seance_index', [], Response::HTTP_SEE_OTHER);
        }
        else
        {
            dump($form->getErrors(true, false));
  
        return $this->renderForm('seance/new.html.twig', [
            'seance' => $seance,
            'form' => $form,
            'seances' => $seanceRepository->findAll(),
        ]);
        }*/
        $seance = new Seance();
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageseance')->getData();
            
                if ($imageFile) {
                    // Déplacer le fichier téléchargé vers le répertoire spécifié
                    /*$uploadsDirectory = 'C:/xampp/htdocs/imageProjet'; // Chemin absolu vers le répertoire souhaité
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $uploadsDirectory . '/' . $originalFilename . '.' . $imageFile->guessExtension();
                    $imageFile->move($uploadsDirectory, $originalFilename . '.' . $imageFile->guessExtension());
                        // Définir le chemin complet du fichier image dans l'entité Seance
                    $seance->setImageseance($newFilename);*/
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename.'.'.$imageFile->guessExtension();
                    // Move the file to the desired directory
                    try {
                        $imageFile->move(
                            'C:/xampp/htdocs/imageProjet',
                            $newFilename
                        );
                    } catch (FileException $e) {
                        // Handle the exception if unable to move the file
                        // For example, log an error message or display a flash message
                        // and return a response to inform the user of the error
                        // Note: You may need to handle this differently based on your application's requirements
                        $this->addFlash('error', 'Failed to upload the image file.');
                        return $this->redirectToRoute('app_reservationb');
    
                    }
        
                    // Update the 'image' property of the Exposition entity with the new filename
                    $seance->setImageseance($newFilename);
                
            }
            $entityManager->persist($seance);
            $entityManager->flush();

            return $this->redirectToRoute('app_seance_index', [], Response::HTTP_SEE_OTHER);
        }

        // Gestion des erreurs de formulaire
        dump($form->getErrors(true, false));

        return $this->renderForm('seance/new.html.twig', [
            'seance' => $seance,
            'form' => $form,
            'seances' => $seanceRepository->findAll(),
        ]);
      
 }

    
  

    #[Route('/{idseance}', name: 'app_seance_show', methods: ['GET'])]
    public function show(Seance $seance): Response
    {
        return $this->render('seance/show.html.twig', [
            'seance' => $seance,
        ]);
    }

    #[Route('/{idseance}/edit', name: 'app_seance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Seance $seance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);
        $imageName = $seance->getImageseance();
        $imagePath = $imageName ? $this->getParameter('uploads_directory') . '/' . $imageName : null;
        
        if ($form->isSubmitted() && $form->isValid()) {
             $imageFile = $form->get('imageseance')->getData();
            if ($imageFile) {
                // Move uploaded file to desired location
                // Example:
                $uploadsDirectory = $this->getParameter('uploads_directory');
                $imageFileName = md5(uniqid()) . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $uploadsDirectory,
                    $imageFileName
                );
                // Set image file name to user entity
                $seance->setImageseance($imageFileName);
            }
            $entityManager->flush();

            return $this->redirectToRoute('app_seance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('seance/edit.html.twig', [
            'seance' => $seance,
            'form' => $form,
            'image_path' => $imagePath,

        ]);
    }

    #[Route('/{idseance}', name: 'app_seance_delete', methods: ['POST'])]
    public function delete(Request $request, Seance $seance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$seance->getIdseance(), $request->request->get('_token'))) {
            $entityManager->remove($seance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_seance_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/{idseance}/seanceimage', name: 'seanceimage')]
    public function imageseance(SeanceRepository $seanceRepository): Response
    {    // Récupérer toutes les entrées de la table seance
        $seances = $seanceRepository->findAll();

        // Tableau pour stocker les chemins des images
        $images = [];

        // Parcourir toutes les entrées de la table seance et extraire les chemins des images
        foreach ($seances as $seance) {
            // Vérifier si le champ imageseance contient un chemin d'image non vide
            if ($seance->getImageseance() !== null) {
                // Ajouter le chemin de l'image au tableau des images
                $images[] = $seance->getImageseance();
            }
        }

        return $this->render('seance/index.html.twig', [
            'images' => $images, // Passer le tableau des chemins d'images à la vue Twig
        ]);
    }
    #[Route('/about', name: 'app_about')]
    public function about(): Response
    {
     //  return new Response("the about page");
     return $this->redirectToRoute('app_about', [], Response::HTTP_SEE_OTHER);
    } 
    #[Route('/pl', name: 'app_planning')]
    public function planning(EntityManagerInterface $entityManager): Response
    {
        // Récupérer le jour de la semaine actuel avec la première lettre en majuscule et les autres en minuscule
        $currentDay = ucfirst(strtolower(date('l')));

        // Récupérer toutes les séances de la journée actuelle depuis la base de données
        $seancesRepository = $entityManager->getRepository(Seance::class);
        $seances = $seancesRepository->findBy(['jourseance' => $currentDay]);

        return $this->render('reservation/reservation.html.twig', [
            'seances' => $seances,
        ]);
    }
  

}
