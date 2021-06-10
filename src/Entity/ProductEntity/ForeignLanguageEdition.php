<?php

namespace App\Entity\ProductEntity;

class ForeignLanguageEdition
{

    private int $code;

    private string $label;

    private ?string $libriCode;

    private ?string $secondLibriCode;

    private ?bool $isFrench;

    public function __construct(
        int $code,
        string $label,
        ?string $libriCode = null,
        ?string $secondLibriCode = null,
        ?bool $isFrench = null
    ) {
        $this->code = $code;
        $this->label = $label;
        $this->libriCode = $libriCode;
        $this->secondLibriCode = $secondLibriCode;
        $this->isFrench = $isFrench;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getLibriCode(): ?string
    {
        return $this->libriCode;
    }

    public function getSecondLibriCode(): ?string
    {
        return $this->secondLibriCode;
    }

    public function isFrench(): ?bool
    {
        return $this->isFrench;
    }
}
