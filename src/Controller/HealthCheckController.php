<?php

declare(strict_types=1);

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Throwable;

/**
 * Healthcheck für Docker (Dienst web) und die externe Überwachung: prüft den Weg bis zur Datenbank.
 */
#[AsController]
final readonly class HealthCheckController
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    #[Route('/healthz', name: 'app_health', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        try {
            $this->connection->executeQuery('SELECT 1');
            $database = 'ok';
        } catch (Throwable) {
            $database = 'unavailable';
        }

        $response = new JsonResponse(
            ['status' => 'ok' === $database ? 'ok' : 'degraded', 'database' => $database],
            'ok' === $database ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE,
        );
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
