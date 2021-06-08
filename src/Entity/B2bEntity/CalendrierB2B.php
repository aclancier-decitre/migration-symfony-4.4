<?php

namespace App\Entity\B2bEntity;

class CalendrierB2B implements \JsonSerializable
{
    /**
     * @var string|null
     */
    private $libelle;

    /**
     * @var Famille[]
     */
    private $famillesAssignees = [];

    /**
     * @var Periode[]
     */
    private $periodes = [];

    public function __construct()
    {
        $this->famillesAssignees = [];
        $this->periodes = [];
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    /**
     * @param string $libelle
     * @return self
     */
    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getFamillesAssignees(): array
    {
        return $this->famillesAssignees;
    }

    /**
     * @param $famillesAssignees
     * @return self
     */
    public function setFamillesAssignees($famillesAssignees): self
    {
        $this->famillesAssignees = $famillesAssignees;
        return $this;
    }

    /**
     * Retourne la liste des codes des familles assignées
     * @return array
     */
    public function getCodesFamillesAssignees()
    {
        $codes_familles = [];
        foreach ($this->getFamillesAssignees() as $famille) {
            $codes_familles[] = $famille->getCode();
        }
        return $codes_familles;
    }

    /**
     * @param Famille $famille
     * @return self
     */
    public function addFamilleAssignee(Famille $famille): self
    {
        $this->famillesAssignees[] = $famille;
        return $this;
    }

    /**
     * @param string $codeFamille
     * @return bool
     */
    public function hasCodeFamilleInFamillesAssignees(string $codeFamille): bool
    {
        $result = array_intersect([$codeFamille], $this->getCodesFamillesAssignees());
        return count($result) > 0;
    }

    /**
     * @return Periode[]
     */
    public function getPeriodes(): array
    {
        return $this->periodes;
    }

    /**
     * @param Periode[] $periodes
     * @return self
     */
    public function setPeriodes(array $periodes): self
    {
        $this->periodes = $periodes;
        return $this;
    }

    /**
     * @param Periode $periode
     * @return CalendrierB2B
     */
    public function addPeriode(Periode $periode): self
    {
        $this->periodes[] = $periode;
        return $this;
    }

    /**
     * Défini la manière dont est serializée la classe
     * @return array
     */
    public function jsonSerialize(): array
    {
        return [
            'libelle' => $this->libelle,
            'familles' => $this->famillesAssignees,
            'periodes' => $this->periodes
        ];
    }
}
