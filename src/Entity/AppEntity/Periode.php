<?php

namespace App\Entity\AppEntity;

class Periode
{
    private ?\DateTime $dateDebut = null;
    private ?\DateTime $dateFin = null;

    public function __construct(?\DateTime $dateDebut, ?\DateTime $dateFin)
    {
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
    }


    public function getDateDebut(): ?\DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(?\DateTime $dateDebut)
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(?\DateTime $dateFin)
    {
        $this->dateFin = $dateFin;
        return $this;
    }
}
