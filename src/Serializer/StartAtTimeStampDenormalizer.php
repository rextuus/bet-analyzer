<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\TipicoBet;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @author Wolfgang Hinzmann <wolfgang.hinzmann@doccheck.com>
 * @license 2024 DocCheck Community GmbH
 */
class StartAtTimeStampDenormalizer implements DenormalizerInterface
{
    public function supportsDenormalization($data, string $type, string $format = null, array $context = []): bool
    {
        // Check if the data is being denormalized into TipicoBet and contains startAtTimeStamp
        return $type === TipicoBet::class && isset($data['startAtTimeStamp']);
    }

    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        // Convert startAtTimeStamp to bigint if it's a string
        $data['startAtTimeStamp'] = (int) $data['startAtTimeStamp'];
        return $data;
    }
}
