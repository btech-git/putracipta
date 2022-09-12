<?php

namespace App\Entity\AccessPoint;

use App\Repository\AccessPoint\ItemBoxKeyRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ItemBoxKeyRepository::class)]
class ItemBoxKey
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $abc = null;

    #[ORM\Column]
    private ?int $def = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $ghi = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $jkl = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $mno = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $pqrs = null;

    #[ORM\Column]
    private ?bool $tuv = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $wxyz = null;

    #[ORM\Column(type: Types::ARRAY)]
    private array $qwerty = [];

    #[ORM\Column(type: Types::OBJECT)]
    private ?object $dvorak = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAbc(): ?string
    {
        return $this->abc;
    }

    public function setAbc(string $abc): self
    {
        $this->abc = $abc;

        return $this;
    }

    public function getDef(): ?int
    {
        return $this->def;
    }

    public function setDef(int $def): self
    {
        $this->def = $def;

        return $this;
    }

    public function getGhi(): ?string
    {
        return $this->ghi;
    }

    public function setGhi(string $ghi): self
    {
        $this->ghi = $ghi;

        return $this;
    }

    public function getJkl(): ?\DateTimeInterface
    {
        return $this->jkl;
    }

    public function setJkl(?\DateTimeInterface $jkl): self
    {
        $this->jkl = $jkl;

        return $this;
    }

    public function getMno(): ?\DateTimeInterface
    {
        return $this->mno;
    }

    public function setMno(?\DateTimeInterface $mno): self
    {
        $this->mno = $mno;

        return $this;
    }

    public function getPqrs(): ?\DateTimeInterface
    {
        return $this->pqrs;
    }

    public function setPqrs(?\DateTimeInterface $pqrs): self
    {
        $this->pqrs = $pqrs;

        return $this;
    }

    public function isTuv(): ?bool
    {
        return $this->tuv;
    }

    public function setTuv(bool $tuv): self
    {
        $this->tuv = $tuv;

        return $this;
    }

    public function getWxyz(): ?string
    {
        return $this->wxyz;
    }

    public function setWxyz(string $wxyz): self
    {
        $this->wxyz = $wxyz;

        return $this;
    }

    public function getQwerty(): array
    {
        return $this->qwerty;
    }

    public function setQwerty(array $qwerty): self
    {
        $this->qwerty = $qwerty;

        return $this;
    }

    public function getDvorak(): ?object
    {
        return $this->dvorak;
    }

    public function setDvorak(object $dvorak): self
    {
        $this->dvorak = $dvorak;

        return $this;
    }
}
