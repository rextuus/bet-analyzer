<?php
declare(strict_types=1);

namespace App\Service\Evaluation\Message;

use App\Service\Statistic\Content\OddOutcome\OutcomeCalculator;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;


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
