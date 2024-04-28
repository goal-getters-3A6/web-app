<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\CalorieCalculatorType;
use App\Repository\PlatRepository;

class CalorieCalculatorController extends AbstractController
{
    #[Route('/calorie-calculator', name: 'calorie_calculator')]
    public function index(Request $request, PlatRepository $platRepository): Response
    {
        $form = $this->createForm(CalorieCalculatorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $calories = $this->calculateCalories($data);

            // Fetch plats within the calorie range
            $platsInRange = $platRepository->findByCaloriesRange($calories - 1000, $calories + 1000); // Adjust the range as needed

            return $this->render('calorie_calculator/result.html.twig', [
                'calories' => $calories,
                'platsInRange' => $platsInRange,
            ]);
        }

        return $this->render('calorie_calculator/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function calculateCalories(array $userData): int
    {
        // Example calculation (replace with your own algorithm)
        $calories = 0;

        // Sample calculation: Basal Metabolic Rate (BMR) calculation using Mifflin-St Jeor equation
        if ($userData['gender'] === 'male') {
            $calories = 10 * $userData['weight'] + 6.25 * $userData['age'] - 5 * $userData['age'] + 5;
        } else {
            $calories = 10 * $userData['weight'] + 6.25 * $userData['age'] - 5 * $userData['age'] - 161;
        }

        // Adjust calories for pregnancy status if necessary
        if ($userData['pregnant']) {
            $calories += 300; // Additional 300 calories recommended during pregnancy
        }

        return $calories;
    }
}
