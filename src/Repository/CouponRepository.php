<?php

declare(strict_types=1);

namespace SystemeioTestTask\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SystemeioTestTask\Entity\Coupon;

/**
 * @extends ServiceEntityRepository<Coupon>
 *
 * @psalm-api
 */
final class CouponRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Coupon::class);
    }

    public function findOneByCode(string $code): ?Coupon
    {
        return $this->findOneBy(['code' => $code]);
    }
}
