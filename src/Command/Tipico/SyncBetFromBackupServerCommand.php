<?php

namespace App\Command\Tipico;

use App\Entity\BettingProvider\TipicoBet;
use App\Service\Tipico\Api\Response\TipicoDailyMatchesResponse;
use App\Service\Tipico\Content\TipicoBet\Data\TipicoBetData;
use App\Service\Tipico\Content\TipicoOdd\BothTeamsScoreOdd\Data\TipicoBothTeamsScoreOddData;
use App\Service\Tipico\Content\TipicoOdd\HeadToHeadOdd\Data\TipicoHeadToHeadOddData;
use App\Service\Tipico\Content\TipicoOdd\OverUnderOdd\Data\TipicoOverUnderOddData;
use App\Service\Tipico\TipicoBetSimulationService;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Serializer\SerializerInterface;

#[AsCommand(
    name: 'SyncBetFromBackupServer',
    description: 'Add a short description for your command',
)]
class SyncBetFromBackupServerCommand extends Command
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly TipicoBetSimulationService $betSimulationService
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Sync Tipico bets from remote server.');
        $this->addArgument('from', InputArgument::REQUIRED, 'from');
        $this->addOption('dry', 'd');
        $this->addOption('update', 'u');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getArgument('from');
        if (!preg_match('~^[0-9]{2}\.[0-9]{2}\.[0-9]{4}$~', $from)){
            $output->writeln('Need date in format dd.mm.YYY');
            return Command::FAILURE;
        }

        $fromDate = (new DateTime($from));
        $untilDate = clone $fromDate;

        $fromDate->setTime(0,0);
        $untilDate->setTime(23, 59, 59);

        $fromDateTimeStamp = $fromDate->getTimestamp() * 1000;
        $untilDateTimeStamp = $untilDate->getTimestamp() * 1000;

        $output->writeln(sprintf('Sync bet from %s to %s', $fromDate->format('d/m/y H:i:s'), $untilDate->format('d/m/y H:i:s')));

        // Call the API endpoint to fetch Tipico bets
        $httpClient = HttpClient::create();
        $server = '35.246.218.255/bet-analyzer/public/index.php';
        $response = $httpClient->request('GET', "http://$server/api/bets?from=$fromDateTimeStamp&until=$untilDateTimeStamp");

        // Deserialize the JSON response into TipicoBet entities
        $tipicoBetsData = $response->toArray();
        $tipicoBets = [];
        $overUnderOdds = [];
        $bothTeamsScoreOdds = [];
        $headToHeadOdds = [];

        foreach ($tipicoBetsData as $betData) {
            $rawResponse = $this->serializer->deserialize(json_encode($betData), TipicoBet::class, 'json');
            $tipicoBets[] = (new TipicoBetData())->initFromApiResponse($rawResponse);
            if (array_key_exists('tipicoOverUnderOdds', $rawResponse)) {
                foreach ($rawResponse['tipicoOverUnderOdds'] as $overUnderOdd) {
                    $overUnderOdds[] = (new TipicoOverUnderOddData())->initFromApiResponse($overUnderOdd, $betData['tipicoId']);
                }
            }
            if (array_key_exists('tipicoBothTeamsScoreBet', $rawResponse) && $rawResponse['tipicoBothTeamsScoreBet'] !== null) {
                $bothTeamsScoreOdds[] = (new TipicoBothTeamsScoreOddData())->initFromApiResponse($rawResponse['tipicoBothTeamsScoreBet'], $betData['tipicoId']);
            }
            if (array_key_exists('tipicoHeadToHeadScore', $rawResponse) && $rawResponse['tipicoHeadToHeadScore'] !== null) {
                $headToHeadOdds[] = (new TipicoHeadToHeadOddData())->initFromApiResponse($rawResponse['tipicoHeadToHeadScore'], $betData['tipicoId']);
            }
        }
        $container = new TipicoDailyMatchesResponse(json_decode($response->getContent(), true));
        $container->setMatches($tipicoBets);
        $container->setOverUnderOdds($overUnderOdds);
        $container->setBothTeamsScoreOdds($bothTeamsScoreOdds);
        $container->setHeadToHeadOdds($headToHeadOdds);

        $dry = $input->getOption('dry');
        if ($dry){
            dump($container);
            return Command::SUCCESS;
        }

        $update = $input->getOption('dry');
        if ($update) {
            return Command::SUCCESS;
        }

        $created = $this->betSimulationService->processDailyMatchesResponse($container);
        $output->writeln($created);

        return Command::SUCCESS;
    }
}
