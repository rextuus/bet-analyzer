<?php

namespace App\Twig\Components;

use App\Entity\BettingProvider\TipicoBet;
use App\Service\Evaluation\BetOn;
use App\Service\Tipico\Content\Simulator\SimulatorService;
use App\Service\Tipico\SimulationProcessors\AbstractSimulationProcessor;
use App\Service\Tipico\SimulationProcessors\OverUnderStrategy;
use App\Twig\Components\Helper\FixruteVisualizationTrait;
use DateTime;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent]
final class CombinedPlacement
{
    use FixruteVisualizationTrait;

    /**
     * @var array<string, TipicoBet>
     */
    public array $combinedPlacement;

    public function __construct(private SimulatorService $simulatorService)
    {
    }

    public function getBetOn(): string
    {
        $simulatorIdent = array_key_first($this->combinedPlacement);
        $simulator = $this->simulatorService->findByIdentifier($simulatorIdent);
        $fixture = $this->combinedPlacement[$simulatorIdent];

        $strategy = $simulator->getStrategy();
        $parameters = json_decode($strategy->getParameters(), true);

        $targetBeton = BetOn::from($parameters[AbstractSimulationProcessor::PARAMETER_TARGET_BET_ON]);

        $overUnderTarget = 0.0;
        if (array_key_exists(OverUnderStrategy::PARAMETER_TARGET_VALUE, $parameters)) {
            $overUnderTarget = $parameters[OverUnderStrategy::PARAMETER_TARGET_VALUE];
        }


        $start = (new DateTime())->setTimestamp($fixture->getStartAtTimeStamp() / 1000);

        $startedClass = $this->getStartedCssClass($fixture);

        $matchInfo = $this->getMatchInfo($fixture, true, $targetBeton, $overUnderTarget);

        $cssClasses = $this->calculateOddMatrix($simulator);

        return $this->calculateHtml(
            $matchInfo,
            $startedClass,
            $start,
            $this->getTimeDistance($fixture),
            $fixture,
            $cssClasses,
        );
    }

    public function getSimulatorIdents(): string
    {
        return implode('<br>', array_keys($this->combinedPlacement));
    }
}
