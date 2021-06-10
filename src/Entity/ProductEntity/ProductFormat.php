<?php

namespace App\Entity\ProductEntity;

class ProductFormat
{

    private string $code;

    private string $label;

    public function __construct(string $code, string $label)
    {
        $this->code = $code;
        $this->label = $label;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLabel(): string
    {
        return $this->label;
    }
}
