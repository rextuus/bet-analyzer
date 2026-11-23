<?php

declare(strict_types=1);

use App\Service\Tipico\Api\Response\TipicoDailyMatchesResponse;
use PHPUnit\Framework\TestCase;

/**
 * Prüft den Parser gegen einen Ausschnitt der echten Tipico-Antwort (programgateway, Stand 20.11.2026).
 * Schlägt der Test nach einem Update der Fixture fehl, hat Tipico das Format geändert.
 */
class TipicoDailyMatchesResponseTest extends TestCase
{
    public function testParsesMatchesAndOddsFromProgramGatewayResponse(): void
    {
        $data = json_decode(
            file_get_contents(__DIR__ . '/Fixtures/tipico-hour-events-2026-11.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $response = new TipicoDailyMatchesResponse($data);
        $response->parseResponse();

        $matches = $response->getMatches();
        self::assertCount(2, $matches);

        $first = $matches[0];
        self::assertSame(732342710, $first->getTipicoId());
        self::assertSame('Real Cartagena', $first->getHomeTeamName());
        self::assertSame('Tigres FC', $first->getAwayTeamName());
        self::assertSame(1.1, $first->getOddHome());
        self::assertSame(6.0, $first->getOddDraw());
        self::assertSame(14.0, $first->getOddAway());
        self::assertFalse($first->isFinished());

        self::assertCount(4, $response->getOverUnderOdds());
        self::assertCount(2, $response->getBothTeamsScoreOdds());
        // head-to-head liefert Tipico im programgateway nicht mehr
        self::assertCount(0, $response->getHeadToHeadOdds());
    }
}
