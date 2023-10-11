<?php

namespace App\Service\Evaluation\Message;

final class CalculateSummariesMessage
{
    private int $seasonApiId;



    /*
     * Add whatever properties and methods you need
     * to hold the data for this message class.
     */

//     private $name;

//     public function __construct(string $name)
//     {
//         $this->name = $name;
//     }

//    public function getName(): string
//    {
//        return $this->name;
//    }
    /**
     * @param int $seasonId
     */
    public function __construct(int $seasonId)
    {
        $this->seasonApiId = $seasonId;
    }

    public function getSeasonApiId(): int
    {
        return $this->seasonApiId;
    }

    public function setSeasonApiId(int $seasonApiId): CalculateSummariesMessage
    {
        $this->seasonApiId = $seasonApiId;
        return $this;
    }
}
