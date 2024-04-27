<?php

declare(strict_types=1);

namespace App\Service\BettingProvider\Betano\Content\BetanoBet;

use App\Entity\BettingProvider\BetanoBet;
use App\Service\BettingProvider\AbstractBettingProviderService;
use App\Service\BettingProvider\Betano\Api\Response\DailyMatchEventResponse;
use App\Service\BettingProvider\Betano\Content\BetanoBet\Data\BetanoBetData;
use App\Service\BettingProvider\BettingProvider;
use App\Service\BettingProvider\BettingProviderBackupFile\BettingProviderApiResponseInterface;
use App\Service\BettingProvider\BettingProviderBackupFile\BettingProviderServiceInterface;
use App\Service\BettingProvider\BettingProviderBackupFile\Content\BettingProviderBackupFileService;
use App\Service\Tipico\Content\TipicoBet\TipicoBetService;


class BetanoBetService extends AbstractBettingProviderService implements BettingProviderServiceInterface
{
    private BettingProvider $bettingProvider = BettingProvider::BETANO;

    public function __construct(
        private readonly TipicoBetService $tipicoBetService,
        private readonly BettingProviderBackupFileService $bettingProviderBackupFileService,
        private readonly BetanoBetRepository $repository,
        private readonly BetanoBetFactory $factory
    ) {
        parent::__construct(
            $this->tipicoBetService,
            $this->bettingProviderBackupFileService,
        );
    }

    public function createByData(BetanoBetData $data): BetanoBet
    {
        $betanoBet = $this->factory->createByData($data);
        $this->repository->save($betanoBet);
        return $betanoBet;
    }

    public function update(BetanoBet $betanoBet, BetanoBetData $data): BetanoBet
    {
        $betanoBet = $this->factory->mapData($data, $betanoBet);
        $this->repository->save($betanoBet);
        return $betanoBet;
    }

    /**
     * @return BetanoBet[]
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
