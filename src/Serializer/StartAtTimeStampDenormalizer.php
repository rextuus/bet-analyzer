<?php
declare(strict_types=1);

namespace App\Serializer;

use App\Entity\BettingProvider\TipicoBet;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;


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
