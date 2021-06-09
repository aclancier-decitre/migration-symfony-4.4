<?php

namespace App\Entity\ProductEntity;

class ProductType
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $label;

    public const PLV_CODE = 'PL';
    public const PAPETERIE_CODE = 'P';
    public const LIVRE_NUMERIQUE_CODE = 'LN';
    public const CODE_PRODUIT_LN = 'LN';

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
