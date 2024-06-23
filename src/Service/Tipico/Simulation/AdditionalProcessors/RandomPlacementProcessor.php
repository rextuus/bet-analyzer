<?php

declare(strict_types=1);

namespace App\Service\Tipico\Simulation\AdditionalProcessors;

use App\Service\Tipico\Content\Placement\Data\TipicoPlacementData;
use App\Service\Tipico\Simulation\Data\AdditionalProcessResult;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class RandomPlacementProcessor
{
    /**
     * @param TipicoPlacementData[] $placementData
     * @param float[] $inputVariants
     */
    public function process(
        array $placementData,
        array $inputVariants,
    ): AdditionalProcessResult {
        foreach ($placementData as $placement) {
            $input = (float)$inputVariants[array_rand($inputVariants)];
            $placement->setInput($input);
        }

        $result = new AdditionalProcessResult();

        $result->setPlacementData($placementData);

        return $result;
    }
}
