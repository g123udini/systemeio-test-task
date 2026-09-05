<?php

declare(strict_types=1);

namespace SystemeioTestTask\Entity;

use Doctrine\ORM\Mapping as ORM;
use SystemeioTestTask\Repository\ProductRepository;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column]
    private int $priceCents;

    public function __construct(string $name, int $priceCents)
    {
        $this->name = $name;
        $this->priceCents = $priceCents;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPriceCents(): int
    {
        return $this->priceCents;
    }
}
