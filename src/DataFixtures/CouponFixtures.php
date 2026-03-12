<?php

namespace App\DataFixtures;

use App\Entity\Coupon;
use App\Enum\CouponType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CouponFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $coupons = [
            ['code' => 'P10', 'type' => CouponType::PERCENTAGE_OF_PURCHASE, 'value' => 10],
            ['code' => 'P100', 'type' => CouponType::PERCENTAGE_OF_PURCHASE, 'value' => 100],
            ['code' => 'D15', 'type' => CouponType::PERCENTAGE_OF_PURCHASE, 'value' => 15],
            ['code' => 'F5', 'type' => CouponType::FIXED_DISCOUNT, 'value' => 500],
        ];

        foreach ($coupons as $data) {
            $coupon = new Coupon();
            $coupon->setCode($data['code']);
            $coupon->setType($data['type']);
            $coupon->setValue($data['value']);

            $manager->persist($coupon);
        }

        $manager->flush();
    }
}