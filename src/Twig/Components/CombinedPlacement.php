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
     * @var array<string, array<string,TipicoBet>>
     */
    public array $combinedPlacement;

    public function __construct(private SimulatorService $simulatorService)
    {
    }

    public function getBetOn(): string
    {
        $variants = array_keys($this->combinedPlacement);
        if (count($variants) === 1) {
            $placements = $this->combinedPlacement[$variants[0]];

            $simulatorIdent = array_key_first($placements);
            $simulator = $this->simulatorService->findByIdentifier($simulatorIdent);
            $cssClasses = $this->calculateOddMatrix($simulator);

            return $this->getHtmlForBetVariant($placements, $cssClasses);
        }

        // we have multiple bet variant for the same game
        $cssClasses = [];
        foreach ($this->combinedPlacement as $variantName => $variant) {
            $simulatorIdent = array_key_first($variant);
            $simulator = $this->simulatorService->findByIdentifier($simulatorIdent);
            $cssClasses[] = $this->calculateOddMatrix($simulator);
        }

        $combined = [];

        foreach ($cssClasses as $set) {
            foreach ($set as $key => $values) {
                if (!isset($combined[$key])) {
                    $combined[$key] = $values;
                } else {
                    // Merge arrays
                    foreach ($values as $index => $value) {
                        if (!isset($combined[$key][$index]) || str_contains($value, 'is-target')) {
                            $combined[$key][$index] = $value;
                        }
                    }
                }
            }
        }
//
//        dump($cssClasses);
//        dd($combined);
        $placements = $this->combinedPlacement[$variants[0]];

        return $this->getHtmlForBetVariant($placements, $combined);
    }

    public function getSimulatorIdents(): string
    {
        $variants = array_keys($this->combinedPlacement);
        if (count($variants) === 1) {
            $placements = $this->combinedPlacement[$variants[0]];

            return implode('<br>', array_keys($placements));
        }

        $simulatorIdentifiers = [];
        foreach ($this->combinedPlacement as $variantName => $variant) {
            $simulatorIdentifiers[] = $variantName . ' => ' . implode('<br>', array_keys($variant));
        }

        return implode('<br><br>', $simulatorIdentifiers);
    }

    /**
     * @param array $placements
     * @return void
     * @throws \Exception
     */
    private function getHtmlForBetVariant(array $placements, array $cssClasses): string
    {
        $simulatorIdent = array_key_first($placements);
        $simulator = $this->simulatorService->findByIdentifier($simulatorIdent);
        $fixture = $placements[$simulatorIdent];

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

        return $this->calculateHtml(
            $matchInfo,
            $startedClass,
            $start,
            $this->getTimeDistance($fixture),
            $fixture,
            $cssClasses,
        );
    }
}
