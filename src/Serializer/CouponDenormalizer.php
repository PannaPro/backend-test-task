<?php

declare(strict_types=1);

namespace App\Serializer;

use App\Entity\Coupon;
use App\Repository\CouponRepository;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

final class CouponDenormalizer implements DenormalizerInterface
{
    public function __construct(
        private readonly CouponRepository $couponRepository,
    ) {}

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        return $this->couponRepository->findOneBy(['code' => $data]);
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        return is_string($data) && $type === Coupon::class;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [Coupon::class => true];
    }
}
