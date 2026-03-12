<?php

namespace App\Repository;

use App\Entity\Coupon;
use App\Service\Exception\DomainNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Coupon>
 */
class CouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    public function getByCodeOrFail(string $code): Coupon
    {
        /** @var Coupon|null $coupon */
        $coupon = $this->findOneBy(['code' => $code]);
        if ($coupon === null) {
            throw DomainNotFoundException::notFound();
        }

        return $coupon;
    }
}
