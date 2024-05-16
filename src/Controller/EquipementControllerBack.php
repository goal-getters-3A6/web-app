<?php

namespace App\Controller;


use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

#[Route('/eqb')]
class EquipementControllerBack extends AbstractController
{
    #[Route('/user-images/{imageName}', name: 'user_images')]
public function getUserImage(string $imageName): Response
{
    $imagePath = 'C:\xampp\htdocs\imageProjet\\' . $imageName;
    
    // Return the image as a response
    return new BinaryFileResponse($imagePath);
}
    #[Route('/', name: 'app_equipement_back_index', methods: ['GET'])]
    public function index(EquipementRepository $equipementRepository): Response
{
    $entityManager = $this->getDoctrine()->getManager();

    $categories = [
        'Fitness' => $entityManager->getRepository(Equipement::class)->countByCategory('Fitness'),
        'Cardiotraining' => $entityManager->getRepository(Equipement::class)->countByCategory('Cardio-training'),
        'Musculation' => $entityManager->getRepository(Equipement::class)->countByCategory('Musculation'),
    ];

    $labels = array_keys($categories);
    $data = array_values($categories);

    return $this->render('equipement/equipementback.html.twig', [
        'equipements' => $equipementRepository->findAll(),
        'labels' => json_encode($labels),
        'data' => json_encode($data),
    ]);
}


#[Route('/filter-by-date', name: 'app_equipement_filter_by_date', methods: ['GET'])]
public function filterByDate(Request $request, EquipementRepository $equipementRepository): Response
{
    $filterDate = new \DateTime($request->query->get('filterDate'));

    
    $equipements = $equipementRepository->findByDatepromainte($filterDate);
    $entityManager = $this->getDoctrine()->getManager();
    $categories = [
        'Fitness' => $entityManager->getRepository(Equipement::class)->countByCategory('Fitness'),
        'Cardiotraining' => $entityManager->getRepository(Equipement::class)->countByCategory('Cardio-training'),
        'Musculation' => $entityManager->getRepository(Equipement::class)->countByCategory('Musculation'),
    ];


    $labels = array_keys($categories);
    $data = array_values($categories);


    return $this->render('equipement/equipementback.html.twig', [
        'equipements' => $equipements,
        'labels' => json_encode($labels),
        'data' => json_encode($data),
      
    ]);
}



    #[Route('/new', name: 'app_equipement_back_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {

    $equipement = new Equipement();
    $form = $this->createForm(EquipementType::class, $equipement);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form['imageeq']->getData();
    
        if ($imageFile) {
           /* $newFilename = uniqid().'.'.$imageFile->guessExtension();
   
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            
    
            $equipement->setImageeq($newFilename);*/
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
                    $equipement->setImageeq($newFilename);        }
    
        $entityManager->persist($equipement);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_equipement_back_index');
    }
    

    return $this->render('equipement/newBack.html.twig', [
        'equipement' => $equipement,
        'form' => $form->createView(),
    ]);
    }

    #[Route('/{idEq}', name: 'app_equipement_back_show', methods: ['GET'])]
    public function show(Equipement $equipement): Response
    {
        return $this->render('equipement/showBack.html.twig', [
            'equipement' => $equipement,
        ]);
    }

    #[Route('/{idEq}/edit', name: 'app_equipement_back_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EquipementType::class, $equipement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            
        $imageFile = $form['imageeq']->getData();
    
        
        if ($imageFile) {
            
           /* $newFilename = uniqid().'.'.$imageFile->guessExtension();
    
            
           
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            
    
         
            $equipement->setImageeq($newFilename);*/

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
            $equipement->setImageeq($newFilename);    
        }
    
        $entityManager->persist($equipement);
        $entityManager->flush();

            return $this->redirectToRoute('app_equipement_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('equipement/edit.html.twig', [
            'equipement' => $equipement,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{idEq}', name: 'app_equipement_delete', methods: ['POST'])]
public function delete(Request $request, Equipement $equipement, EntityManagerInterface $entityManager): Response
{
        if ($this->isCsrfTokenValid('delete'.$equipement->getIdEq(), $request->request->get('_token'))) {
            $entityManager->remove($equipement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_equipement_back_index', [], Response::HTTP_SEE_OTHER);
    }

 
    
      

    #[Route('/eqb/search', name: 'app_equipement_search', methods: ['GET'])]
    public function search(Request $request, EquipementRepository $EquipementRepository): Response
    {
        $criteria = array(
            'nomeq' => $request->query->get('nomeq'), 
            'categeq' => $request->query->get('categeq'), 
           
        );

        
        $equipements = $EquipementRepository->findByCriteria($criteria);

        return $this->render('avisequipement/search_result.html.twig', [
            'avisequipements' => $equipements,
        ]);
    }

    
   

}
