<?php

namespace App\Controller;


use App\Entity\Equipement;
use App\Form\EquipementType;
use App\Repository\EquipementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



#[Route('/eqb')]
class EquipementControllerBack extends AbstractController
{
   
    #[Route('/', name: 'app_equipement_back_index', methods: ['GET'])]
    public function index(EquipementRepository $equipementRepository): Response
{
    $entityManager = $this->getDoctrine()->getManager();

    // Récupération du nombre d'équipements par catégorie spécifique
    $categories = [
        'Fitness' => $entityManager->getRepository(Equipement::class)->countByCategory('Fitness'),
        'Cardiotraining' => $entityManager->getRepository(Equipement::class)->countByCategory('Cardio-training'),
        'Musculation' => $entityManager->getRepository(Equipement::class)->countByCategory('Musculation'),
    ];

    // Formatage des données pour le graphique doughnut
    $labels = array_keys($categories);
    $data = array_values($categories);

    return $this->render('equipement/equipementback.html.twig', [
        'equipements' => $equipementRepository->findAll(),
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
        // Gestion de l'image téléchargée
        $imageFile = $form['imageeq']->getData();
    
        // Vérifie si un fichier a été téléchargé
        if ($imageFile) {
            // Génère un nom de fichier unique
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
   
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            
    
            // Stocke le nom du fichier dans la propriété de l'entité
            $equipement->setImageeq($newFilename);
        }
    
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
            // Gestion de l'image téléchargée
        $imageFile = $form['imageeq']->getData();
    
        // Vérifie si un fichier a été téléchargé
        if ($imageFile) {
            // Génère un nom de fichier unique
            $newFilename = uniqid().'.'.$imageFile->guessExtension();
    
            // Déplace le fichier dans le répertoire où sont stockées les images
           
                $imageFile->move(
                    $this->getParameter('images_directory'),
                    $newFilename
                );
            
    
            // Stocke le nom du fichier dans la propriété de l'entité
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
            'nomeq' => $request->query->get('nomeq'), // Critère : commaeq
            'categeq' => $request->query->get('categeq'), // Critère : idEq.nomeq
           
        );

        // Utiliser le repository pour rechercher avec les critères
        $equipements = $EquipementRepository->findByCriteria($criteria);

        return $this->render('avisequipement/search_result.html.twig', [
            'avisequipements' => $equipements,
        ]);
    }

    #[Route('/eqb/sort/{sortDirection}', name: 'app_equipement_back_sort', methods: ['GET'])]
    public function sort(EntityManagerInterface $entityManager, $sortDirection)
    {
        // Assurez-vous que $sortDirection est soit 'ASC' soit 'DESC'

        $query = $entityManager->createQuery(
            'SELECT e FROM App\Entity\Equipement e ORDER BY e.datepremainte ' . $sortDirection
        );

        $equipements = $query->getResult();

        // Faites quelque chose avec les objets triés, comme les passer à un modèle Twig pour l'affichage

        return $this->render('equipement/equipementback.html.twig', [
            'equipements' => $equipements,
        ]);
    }

   

}
