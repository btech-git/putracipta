<?php

namespace App\Entity;

use App\Entity\Admin\User;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\MappedSuperclass]
abstract class ProductionHeader
{
    public const MONTH_ROMAN_NUMERALS = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];

    #[ORM\Column]
    #[Assert\NotNull]
    protected ?bool $isCanceled = false;

    #[ORM\Column]
    #[Assert\NotNull]
    protected ?bool $isRead = false;

    #[ORM\Column]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    protected ?int $codeNumberOrdinal = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    protected ?int $codeNumberMonth = 0;

    #[ORM\Column(type: Types::SMALLINT)]
    #[Assert\NotNull]
    #[Assert\GreaterThan(0)]
    protected ?int $codeNumberYear = 0;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    #[Assert\NotNull]
    protected ?\DateTimeInterface $createdProductionDateTime = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    protected ?\DateTimeInterface $modifiedProductionDateTime = null;

    #[ORM\ManyToOne]
    #[Assert\NotNull]
    protected ?User $createdProductionUser = null;

    #[ORM\ManyToOne]
    protected ?User $modifiedProductionUser = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    #[Assert\NotNull]
    protected ?\DateTimeInterface $productionDate = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotNull]
    protected ?string $note = '';

    #[ORM\Column(length: 20)]
    private ?string $codeNumberText = '';

    public abstract function getCodeNumberConstant(): string;

    public function getCodeNumber(): string
    {
        $numerals = self::MONTH_ROMAN_NUMERALS;

        return sprintf('%04d/%s/%s/%02d', intval($this->codeNumberOrdinal), $this->getCodeNumberConstant(), $numerals[intval($this->codeNumberMonth)], intval($this->codeNumberYear));
    }

    public function setCodeNumber($codeNumber): self
    {
        $nums = array_flip(self::MONTH_ROMAN_NUMERALS);

        list($ordinal, , $month, $year) = explode('/', $codeNumber);

        $this->codeNumberOrdinal = intval($ordinal);
        $this->codeNumberMonth = $nums[$month];
        $this->codeNumberYear = intval($year);

        return $this;
    }

    public function setCodeNumberToNext($codeNumber, $currentYear, $currentMonth): self
    {
        $this->setCodeNumber($codeNumber);

        $cnMonth = intval($currentMonth);
        $cnYear = intval($currentYear);
        $ordinal = $this->codeNumberOrdinal;
        if ($cnMonth > $this->codeNumberMonth || $cnYear > $this->codeNumberYear) {
            $ordinal = 0;
        }

        $this->codeNumberOrdinal = $ordinal + 1;
        $this->codeNumberMonth = $cnMonth;
        $this->codeNumberYear = $cnYear;

        return $this;
    }

    public function isIsCanceled(): ?bool
    {
        return $this->isCanceled;
    }

    public function setIsCanceled(bool $isCanceled): self
    {
        $this->isCanceled = $isCanceled;

        return $this;
    }

    public function isIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getCodeNumberOrdinal(): ?int
    {
        return $this->codeNumberOrdinal;
    }

    public function setCodeNumberOrdinal(int $codeNumberOrdinal): self
    {
        $this->codeNumberOrdinal = $codeNumberOrdinal;

        return $this;
    }

    public function getCodeNumberMonth(): ?int
    {
        return $this->codeNumberMonth;
    }

    public function setCodeNumberMonth(int $codeNumberMonth): self
    {
        $this->codeNumberMonth = $codeNumberMonth;

        return $this;
    }

    public function getCodeNumberYear(): ?int
    {
        return $this->codeNumberYear;
    }

    public function setCodeNumberYear(int $codeNumberYear): self
    {
        $this->codeNumberYear = $codeNumberYear;

        return $this;
    }

    public function getCreatedProductionDateTime(): ?\DateTimeInterface
    {
        return $this->createdProductionDateTime;
    }

    public function setCreatedProductionDateTime(?\DateTimeInterface $createdProductionDateTime): self
    {
        $this->createdProductionDateTime = $createdProductionDateTime;

        return $this;
    }

    public function getModifiedProductionDateTime(): ?\DateTimeInterface
    {
        return $this->modifiedProductionDateTime;
    }

    public function setModifiedProductionDateTime(?\DateTimeInterface $modifiedProductionDateTime): self
    {
        $this->modifiedProductionDateTime = $modifiedProductionDateTime;

        return $this;
    }

    public function getCreatedProductionUser(): ?User
    {
        return $this->createdProductionUser;
    }

    public function setCreatedProductionUser(?User $createdProductionUser): self
    {
        $this->createdProductionUser = $createdProductionUser;

        return $this;
    }

    public function getModifiedProductionUser(): ?User
    {
        return $this->modifiedProductionUser;
    }

    public function setModifiedProductionUser(?User $modifiedProductionUser): self
    {
        $this->modifiedProductionUser = $modifiedProductionUser;

        return $this;
    }

    public function getProductionDate(): ?\DateTimeInterface
    {
        return $this->productionDate;
    }

    public function setProductionDate(?\DateTimeInterface $productionDate): self
    {
        $this->productionDate = $productionDate;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCodeNumberText(): ?string
    {
        return $this->codeNumberText;
    }

    public function setCodeNumberText(string $codeNumberText): self
    {
        $this->codeNumberText = $codeNumberText;

        return $this;
    }
}
