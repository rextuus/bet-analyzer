<?php

namespace App\Controller;

use App\Service\BettingProvider\Betano\Content\BetanoBackup\BetanoBackupService;
use App\Service\BettingProvider\BettingProvider;
use App\Service\BettingProvider\BettingProviderBackupFile\Content\BettingProviderBackupFileService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api')]

class ApiController extends AbstractController
{
    public function __construct(
        private KernelInterface $kernel,
        private TipicoBetService $tipicoBetService,
        private BetanoBackupService $betanoBackupService,
        private SerializerInterface $serializer,
        private MessageBusInterface $messageBus,
        private BettingProviderBackupFileService $bettingProviderBackupFileService,
    )
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

    #[Route('/betano/daily', name: 'app_api_store_betano_bets', methods: ['POST'])]
    public function storeBetanoBets(Request $request): Response
    {
        $this->bettingProviderBackupFileService->storeNewBackupRelation(BettingProvider::BETANO, $request);

        return new JsonResponse('{stored: true}', Response::HTTP_OK);
    }

    #[Route('/bwin/daily', name: 'app_api_store_bwin_bets', methods: ['POST'])]
    public function storeBwinBets(Request $request): Response
    {
        $this->bettingProviderBackupFileService->storeNewBackupRelation(BettingProvider::BWIN, $request);

        return new JsonResponse('{stored: true}', Response::HTTP_OK);
    }
}
