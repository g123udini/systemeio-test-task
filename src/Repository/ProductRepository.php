<?php

declare(strict_types=1);

namespace SystemeioTestTask\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SystemeioTestTask\Entity\Product;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @psalm-api
 */
final class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }
}
