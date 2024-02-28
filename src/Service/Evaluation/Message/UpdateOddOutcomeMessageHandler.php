<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Message;

use App\Entity\BetRowOddFilter;
use App\Service\Evaluation\BetOn;
use App\Service\Evaluation\OddVariant;
use App\Service\Sportmonks\Content\Fixture\SpmFixtureService;
use App\Service\Sportmonks\Content\Odd\SpmOddService;
use App\Service\Statistic\Content\OddOutcome\OddOutcomeService;
use App\Service\Statistic\Content\OddOutcome\OutcomeCalculator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
#[AsMessageHandler]
class UpdateOddOutcomeMessageHandler
{


    public function __construct(
        private OutcomeCalculator $calculator,
    )
    {
    }

    public function __invoke(UpdateOddOutcomeMessage $updateOddOutcomeMessage)
  {
      $this->calculator->calculateAll($updateOddOutcomeMessage);

  }
}
