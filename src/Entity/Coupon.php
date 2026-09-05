<?php

declare(strict_types=1);

namespace SystemeioTestTask\Entity;

use Doctrine\ORM\Mapping as ORM;
use SystemeioTestTask\Enum\CouponType;
use SystemeioTestTask\Repository\CouponRepository;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 32, unique: true)]
    private string $code;

    #[ORM\Column(enumType: CouponType::class)]
    private CouponType $type;

    #[ORM\Column]
    private int $value;

    public function __construct(string $code, CouponType $type, int $value)
    {
        $this->code = $code;
        $this->type = $type;
        $this->value = $value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getType(): CouponType
    {
        return $this->type;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
