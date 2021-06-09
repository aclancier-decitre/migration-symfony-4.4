<?php

namespace App\Entity\ProductEntity;

class DonneesProduitLn
{
    private string $sku;

    private string $codeFormatLn;

    private string $libelleFormatLn;

    private float $prixTtc;

    private string $codeDispoLn;

    private string $libelleDispoLn;

    private int $codePlateformeLn;

    public function __construct(
        string $sku,
        string $codeFormatLn,
        string $libelleFormatLn,
        float $prixTtc,
        string $codeDispoLn,
        string $libelleDispoLn,
        int $codePlateformeLn
    ) {
        $this->sku = $sku;
        $this->codeFormatLn = $codeFormatLn;
        $this->libelleFormatLn = $libelleFormatLn;
        $this->prixTtc = $prixTtc;
        $this->codeDispoLn = $codeDispoLn;
        $this->libelleDispoLn = $libelleDispoLn;
        $this->codePlateformeLn = $codePlateformeLn;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getPrixTtc(): float
    {
        return $this->prixTtc;
    }
}
