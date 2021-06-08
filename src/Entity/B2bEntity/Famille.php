<?php

namespace App\Entity\B2bEntity;

class Famille
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $libelle;

    public function __construct(string $code, string $libelle)
    {
        $this->code = $code;
        $this->libelle = $libelle;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function __toString()
    {
        return $this->code . ' - ' . $this->libelle;
    }
}
