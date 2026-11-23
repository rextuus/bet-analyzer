<?php

namespace App\Controller;

use App\Service\BettingProvider\BettingProvider;
use App\Service\BettingProvider\BettingProviderBackupFile\Content\BettingProviderBackupFileService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * Daten-API für externe Einlieferer (Betano/Bwin) und den Zweitserver (SyncBetFromBackupServer).
 *
 * Geschützt per Header „X-Api-Token“ (Wert aus API_TOKEN). Ist API_TOKEN leer, ist die API geschlossen –
 * bis 11/2026 war sie ohne jede Prüfung offen (Dateien schreiben, Jobs anstoßen, alle Spiele auslesen).
 */
#[Route('/api')]
class ApiController extends AbstractController
{
    private const TOKEN_HEADER = 'X-Api-Token';

    public function __construct(
        private TipicoBetService $tipicoBetService,
        private SerializerInterface $serializer,
        private BettingProviderBackupFileService $bettingProviderBackupFileService,
        #[Autowire(env: 'default::API_TOKEN')]
        private readonly ?string $apiToken = null,
    )
    {
    }

    #[Route('/bets', name: 'app_api_bet_per_day', methods: ['GET'])]
    public function betPerDay(Request $request): Response
    {
        if (null !== $denied = $this->denyWithoutToken($request)) {
            return $denied;
        }

        $from = $request->query->getInt('from') ?: null;
        $until = $request->query->getInt('until') ?: null;

        $bets = $this->tipicoBetService->findInRange($from, $until);
        $json = $this->serializer->serialize($bets, 'json', ['groups' => ['tipico_bet']]);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    #[Route('/betano/daily', name: 'app_api_store_betano_bets', methods: ['POST'])]
    public function storeBetanoBets(Request $request): Response
    {
        if (null !== $denied = $this->denyWithoutToken($request)) {
            return $denied;
        }

        $this->bettingProviderBackupFileService->storeNewBackupRelation(BettingProvider::BETANO, $request);

        return new JsonResponse(['stored' => true], Response::HTTP_OK);
    }

    #[Route('/bwin/daily', name: 'app_api_store_bwin_bets', methods: ['POST'])]
    public function storeBwinBets(Request $request): Response
    {
        if (null !== $denied = $this->denyWithoutToken($request)) {
            return $denied;
        }

        $this->bettingProviderBackupFileService->storeNewBackupRelation(BettingProvider::BWIN, $request);

        return new JsonResponse(['stored' => true], Response::HTTP_OK);
    }

    private function denyWithoutToken(Request $request): ?JsonResponse
    {
        $given = (string) $request->headers->get(self::TOKEN_HEADER, '');

        if (null === $this->apiToken || '' === $this->apiToken || !hash_equals($this->apiToken, $given)) {
            return new JsonResponse(['error' => 'forbidden'], Response::HTTP_FORBIDDEN);
        }

        return null;
    }
}
