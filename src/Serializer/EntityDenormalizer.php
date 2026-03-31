<?php

declare(strict_types=1);

namespace App\Serializer;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class EntityDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        return $this->em->getRepository($type)->find($data);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        if (!is_int($data)) {
            return false;
        }

        try {
            $this->em->getClassMetadata($type);
            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function getSupportedTypes(?string $format): array
    {
        return ['object' => false];
    }
}
