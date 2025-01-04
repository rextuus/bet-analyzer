<?php

namespace App\Command;

use App\Service\Tipico\Content\Simulator\Data\SimulatorFilterData;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\Content\SimulatorFavoriteList\Data\SimulatorFavoriteListData;
use App\Service\Tipico\Content\SimulatorFavoriteList\SimulatorFavoriteListService;
use App\Service\Tipico\Duplication\SimulatorDuplicationService;
use App\Service\Tipico\Simulation\AdditionalProcessors\Weekday;
use App\Service\Tipico\SimulationProcessors\OverUnderStrategy;
use DateTime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'WeekdayStatistic',
    description: 'Search for the simulators with the highest amount for a specific week-day and creates',
)]
class WeekdayStatisticCommand extends Command
{
    public const POSTFIX = 'ts';

    public function __construct(
        private readonly SimulatorService $simulatorService,
        private readonly SimulatorDuplicationService $duplicationService,
        private readonly SimulatorFavoriteListService $favoriteListService,
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('weekday', InputArgument::OPTIONAL, 'weekday to use', null)
            ->addArgument(
                'excludeWeekdaySpecific',
                InputArgument::OPTIONAL,
                'dont copy simulators already restricted to weekdays',
                true
            )
            ->addArgument(
                'removeDuplicates',
                InputArgument::OPTIONAL,
                'remove over_under duplicates without cash-box differences',
                true
            )
            ->addArgument('min', InputArgument::OPTIONAL, 'min amount of cash earned', 2.0)
            ->addArgument(
                'maxSimulators',
                InputArgument::OPTIONAL,
                'how many simulators should be duplicated in maximum',
                30
            )
            ->addOption('dry-run', 'd');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $weekday = $input->getArgument('weekday');
        $weekday = Weekday::tryFrom($weekday);

        $weekdays = [$weekday];
        if ($weekday === null) {
            $weekdays = Weekday::cases();
        }

        $excludeWeekdaySpecific = $input->getArgument('excludeWeekdaySpecific');
        if ($excludeWeekdaySpecific) {
            $output->writeln('Weekday specific simulators will be excluded');
        }

        $removeDuplicates = $input->getArgument('removeDuplicates');
        if ($removeDuplicates) {
            $output->writeln('Removing duplicates from over/under simulators');
        }

        $minAmountEarned = (float)$input->getArgument('min');
        $output->writeln('Simulators need to have at least a cashbox amount of ' . $minAmountEarned);

        $maxSimulators = $input->getArgument('maxSimulators');
        $output->writeln('At least ' . $maxSimulators . ' will be added to new Favorite Lists');

        $isDryRun = $input->getOption('dry-run');
        if ($isDryRun) {
            $output->writeln('Dry run');
        }

        // create filter to get all simulators
        $filter = new SimulatorFilterData();
        $filter->setMaxResults(1000000);
        $filter->setExcludeNegative(false);

        $filter->setMinCashBox(60.0);
        $simulators = $this->simulatorService->findByFilter($filter);

        foreach ($weekdays as $day) {
            $output->writeln($day->name);
            $simulatorsByCash = [];
            foreach ($simulators as $simulator) {
                // remove weekday specific one
                $skip = false;
                if ($excludeWeekdaySpecific) {
                    foreach (Weekday::cases() as $weekday) {
                        if (str_contains($simulator->getIdentifier(), $weekday->name)) {
                            $skip = true;
                        }
                    }
                }
                if ($skip) {
                    continue;
                }

                // calculate the cashBox amount for the specific day
                $cash = 0.0;
                foreach ($simulator->getTipicoPlacements() as $placement) {
                    if ($placement->getCreated()->format('N') === (string)$day->value) {
                        $cash = $cash - $placement->getInput();
                        if ($placement->isWon()) {
                            $cash = $cash + $placement->getValue();
                        }
                    }
                }
                if ($cash > $minAmountEarned) {
                    $simulatorsByCash[$simulator->getId()] = $cash;
                }
            }

            // sort for the best ones
            arsort($simulatorsByCash);

            // reduce the list to 4 x $maxSimulators amount
            $simulatorsByCash = array_slice($simulatorsByCash, 0, 4 * $maxSimulators, true);

            // remove "duplicates"
            if ($removeDuplicates) {
                $overUnderSimulators = [];

                // sort by exactly cash amount
                foreach ($simulatorsByCash as $id => $cash) {
                    $simulator = $this->simulatorService->find($id);
                    if ($simulator->getStrategy()->getIdentifier() === OverUnderStrategy::IDENT) {
                        $ident = sprintf('%.2f', $cash);
                        if (!array_key_exists($ident, $overUnderSimulators)) {
                            $overUnderSimulators[$ident] = [];
                        }
                        $overUnderSimulators[$ident][$id] = $simulator->getIdentifier();
                    }
                }

                // remove duplicates
                $duplicates = [];
                $seen = [];
                foreach ($overUnderSimulators as $cashAmount => $simulatorsWithSameAmount) {
                    foreach ($simulatorsWithSameAmount as $id => $value) {
                        // Extract the part before `_target` and normalize the search type
                        if (preg_match('/search_([A-Z]+)(?:_\[\d+\])?_([0-9]+_[0-9]+)_target/', $value, $matches)) {
                            $searchType = $matches[1]; // Normalized search type (e.g., "OVER" or "UNDER")
                            $numberPattern = $matches[2]; // The number pattern (e.g., "47_48")

                            // Combine these two as a unique key for grouping
                            $key = "{$searchType}_{$numberPattern}";

                            // Check if we've seen this combination before
                            if (isset($seen[$key])) {
                                // Increment duplicate count
                                $duplicates[$numberPattern][] = [
                                    'id' => $id,
                                    'identifier' => $value,
                                ];
                            } else {
                                // First time seeing this combination
                                $seen[$key] = true;
                            }
                        }
                    }
                }

                foreach ($duplicates as $numberPattern => $similarSimulators) {
                    if (count($similarSimulators) > 1) {
                        // remove simulators from $simulatorsByCash
                        foreach ($similarSimulators as $simulator) {
                            unset($simulatorsByCash[$simulator['id']]);
                        }
                    }
                }
            }

            $counter = 0;
            $newSimulators = [];
            foreach ($simulatorsByCash as $id => $cash) {
                if ($counter < $maxSimulators) {
                    $simulator = $this->simulatorService->find($id);
                    $output->writeln(
                        sprintf(
                            'Copy Simulator %s with win of %.2f',
                            $simulator->getIdentifier(),
                            $simulatorsByCash[$id]
                        )
                    );
                    if (!$isDryRun) {
                        $newSimulators[] = $this->duplicationService->duplicateSimulatorAndLimitToWeekdays(
                            $simulator,
                            [$day],
                            self::POSTFIX,
                        );
                    }
                }
                $counter++;
            }

            if (!$isDryRun) {
                $favoriteListData = new SimulatorFavoriteListData();
                $favoriteListData->setIdentifier('Top_simulators_' . $day->name);
                $favoriteListData->setCreated(new DateTime());
                $favoriteListData->setSimulators($newSimulators);

                $favoriteList = $this->favoriteListService->createByData($favoriteListData);
            }
        }

        return Command::SUCCESS;
    }
}
