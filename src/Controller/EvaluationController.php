<?php

namespace App\Controller;

use App\Form\InitSimpleBetRowsForSeasonData;
use App\Form\InitSimpleBetRowsForSeasonType;
use App\Service\Evaluation\BetRowCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/season/evaluation')]
class EvaluationController extends AbstractController
{
    #[Route('/init', name: 'app_evaluation_init_set')]
    public function index(Request $request, BetRowCalculator $betRowCalculator): Response
    {
        $data = new InitSimpleBetRowsForSeasonData();
        $data->setIncludeTax(true);
        $form = $this->createForm(InitSimpleBetRowsForSeasonType::class, $data);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $data = $form->getData();
            $betRowCalculator->initClassicBetRowSetForSeason($data);
        }

        return $this->render('evaluation/index.html.twig', [
            'form' => $form,
        ]);
    }
}
