<?php

namespace App\Entity\AppEntity;

class ModeTransmission
{
    private string $code;

    private string $libelle;

    private string $typeTraitement;

    public function __construct(string $code, string $libelle, string $typeTraitement)
    {
        $this->code = $code;
        $this->libelle = $libelle;
        $this->typeTraitement = $typeTraitement;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getTypeTraitement(): string
    {
        return $this->typeTraitement;
    }

    public function setTypeTraitement(string $typeTraitement): self
    {
        $this->typeTraitement = $typeTraitement;
        return $this;
    }
}
