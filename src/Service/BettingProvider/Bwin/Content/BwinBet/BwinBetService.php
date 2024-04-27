<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\Bwin\Content\BwinBet;

use App\Entity\BettingProvider\BwinBet;
use App\Service\BettingProvider\AbstractBettingProviderService;
use App\Service\BettingProvider\BettingProvider;
use App\Service\BettingProvider\BettingProviderBackupFile\BettingProviderApiResponseInterface;
use App\Service\BettingProvider\BettingProviderBackupFile\BettingProviderServiceInterface;
use App\Service\BettingProvider\BettingProviderBackupFile\Content\BettingProviderBackupFileService;
use App\Service\BettingProvider\Bwin\Api\Response\DailyMatchEventResponse;
use App\Service\BettingProvider\Bwin\Content\BwinBet\Data\BwinBetData;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;


class BwinBetService extends AbstractBettingProviderService implements BettingProviderServiceInterface
{
    private BettingProvider $bettingProvider = BettingProvider::BWIN;

    public function __construct(
        private readonly TipicoBetService $tipicoBetService,
        private readonly BettingProviderBackupFileService $bettingProviderBackupFileService,
        private readonly BwinBetRepository $repository,
        private readonly BwinBetFactory $factory
    ) {
        parent::__construct(
            $this->tipicoBetService,
            $this->bettingProviderBackupFileService,
        );
    }

    public function createByData(BwinBetData $data): BwinBet
    {
        $bwinBet = $this->factory->createByData($data);
        $this->repository->save($bwinBet);
        return $bwinBet;
    }

    public function update(BwinBet $bwinBet, BwinBetData $data): BwinBet
    {
        $bwinBet = $this->factory->mapData($data, $bwinBet);
        $this->repository->save($bwinBet);
        return $bwinBet;
    }

    /**
     * @return BwinBet[]
     */
    public function findBy(array $conditions): array
    {
        return $this->repository->findBy($conditions);
    }

    public function createResponseToParse(array $content): BettingProviderApiResponseInterface
    {
        return new DailyMatchEventResponse($content);
    }

    public function getProviderIdent(): string
    {
        return $this->bettingProvider->getProviderIdent();
    }

    public function getBettingProvider(): BettingProvider
    {
        return $this->bettingProvider;
    }
}
