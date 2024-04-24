<?php

namespace App\Controller;

use App\Service\Betano\Content\BetanoBackup\BetanoBackupService;
use App\Service\Betano\Message\CollectBetanoFixturesMessage;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;
use BetanoBackupData;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
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
        private MessageBusInterface $messageBus
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
        $jsonData = json_decode($request->getContent(), true);

        $currentDateTime = new DateTime();
        $timestamp = $currentDateTime->format('Y-m-d_H-i-s');

        $backupDir = $this->kernel->getProjectDir() . '/public/backups/betano/' . $currentDateTime->format('m_d_Y');

        $backupFile = $backupDir . '/' . $timestamp . '.json';

        $filesystem = new Filesystem();

        if (!$filesystem->exists($backupDir)) {
            $filesystem->mkdir($backupDir, 0755);
        }

        $filesystem->touch($backupFile);
        $filesystem->appendToFile($backupFile, $request->getContent());

        // store backup entity
        $data = new BetanoBackupData();
        $data->setContainedBets(0);
        $data->setFittedBets(0);
        $data->setNonFittedBets(0);
        $data->setAlreadyStoredBets(0);

        $data->setCreated(new DateTime());
        $data->setIsConsumed(false);
        $data->setFilePath($backupFile);
        $betanoBackup = $this->betanoBackupService->createByData($data);

        // move to event
        $message = new CollectBetanoFixturesMessage($betanoBackup->getId());
        $this->messageBus->dispatch($message);

        return new JsonResponse($jsonData, Response::HTTP_OK);
    }
}
