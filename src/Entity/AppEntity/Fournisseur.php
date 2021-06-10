<?php

namespace App\Entity\AppEntity;

class Fournisseur
{
    private string $code;

    private string $nom;

    private bool $isPrecre;

    private string $dateDerniereModification;

    private ModeTransmission $modeTransmission;

    private ?string $gencod = null;

    private ?string $memo = null;

    public function __construct(
        string $code,
        string $nom,
        bool $isPrecre,
        string $dateDerniereModification,
        ModeTransmission $modeTransmission,
        ?string $gencod,
        ?string $memo
    ) {
        $this->code = $code;
        $this->nom = $nom;
        $this->isPrecre = $isPrecre;
        $this->dateDerniereModification = $dateDerniereModification;
        $this->modeTransmission = $modeTransmission;
        $this->gencod = $gencod;
        $this->memo = $memo;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getGencod(): ?string
    {
        return $this->gencod;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function isPrecre(): bool
    {
        return $this->isPrecre;
    }

    public function getMemo(): ?string
    {
        return $this->memo;
    }

    public function getDateDerniereModification(): string
    {
        return $this->dateDerniereModification;
    }

    public function getModeTransmission(): ModeTransmission
    {
        return $this->modeTransmission;
    }
}
