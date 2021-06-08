<?php

namespace App\Entity\B2bEntity;

class Periode
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var \DateTime
     */
    private $dateDebut;

    /**
     * @var \DateTime
     */
    private $dateFin;

    /**
     * @var int
     */
    private $numeroCommande = 0;

    public function __construct()
    {
        $this->dateDebut = new \DateTime('1970-01-01');
        $this->dateFin = new \DateTime('1970-01-01');
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getDateDebut(): \DateTime
    {
        return $this->dateDebut;
    }

    /**
     * @param \DateTime $dateDebut
     * @return self
     */
    public function setDateDebut(\DateTime $dateDebut): self
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateFin(): \DateTime
    {
        return $this->dateFin;
    }

    /**
     * @param \DateTime $dateFin
     * @return self
     */
    public function setDateFin(\DateTime $dateFin): self
    {
        $this->dateFin = $dateFin;
        return $this;
    }

    public function getNumeroCommande(): int
    {
        return $this->numeroCommande;
    }

    /**
     * @param int $numeroCommande
     * @return self
     */
    public function setNumeroCommande(int $numeroCommande): self
    {
        $this->numeroCommande = $numeroCommande;
        return $this;
    }
}
