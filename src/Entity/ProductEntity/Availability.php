<?php

namespace App\Entity\ProductEntity;

class Availability
{
    private string $code;

    private string $label;

    private string $shortLabel;

    private bool $isOrderingForbidden;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getShortLabel(): string
    {
        return $this->shortLabel;
    }

    public function setShortLabel(string $shortLabel): self
    {
        $this->shortLabel = $shortLabel;
        return $this;
    }

    public function isOrderingForbidden(): bool
    {
        return $this->isOrderingForbidden;
    }

    public function setIsOrderingForbidden(bool $isOrderingForbidden): self
    {
        $this->isOrderingForbidden = $isOrderingForbidden;
        return $this;
    }

    public static function create(string $code, string $label, string $shortLabel, bool $isOrderingForbidden)
    {
        return (new self())->setCode($code)
            ->setLabel($label)
            ->setShortLabel($shortLabel)
            ->setIsOrderingForbidden($isOrderingForbidden);
    }
}
