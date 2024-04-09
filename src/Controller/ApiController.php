<?php

namespace App\Controller;

use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]

class ApiController extends AbstractController
{
    public function __construct(private TipicoBetService $tipicoBetService, private SerializerInterface $serializer)
    {
    }

    #[Route('/bets', name: 'app_api_bet_per_day')]
    public function betPerDay(Request $request): Response
    {
        $from = $request->query->get('from');
        $until = $request->query->get('until');

        $bets = $this->tipicoBetService->findInRange($from, $until);
        $json = $this->serializer->serialize($bets, 'json', ['groups' => ['tipico_bet']]);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }
}
