<?php

namespace App\Entity\ProductEntity;

class Auteur
{
    private ?int $numero = null;

    private ?string $nom = null;

    private ?string $nomPrenom = null;

    private ?string $prenom = null;

    private ?bool $isPersonneMorale = false;

    private ?string $numeroNotice = null;

    private ?int $ordre = null;

    private ?bool $isPrincipal = false;

    private ?string $dateNaissanceDeces = null;

    private ?string $qualification = null;

    private ?PrixNobel $prixNobel = null;

    private ?Nationalite $nationalite = null;

    private ?string $updatedAt = null;

    private ?bool $isBnf = false;

    private ?string $biographie = null;

    private ?string $codeOperateurDerniereModification = null;

    private ?string $codeSiteDerniereModification = null;

    private ?\DateTime $heureDerniereModification = null;

    private ?bool $isBiographieEnrichie = false;

    private ?bool $isBloque = false;


    public function setNumero(?int $numero): self
    {
        $this->numero = $numero;
        return $this;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNomPrenom(?string $nomPrenom): self
    {
        $this->nomPrenom = $nomPrenom;
        return $this;
    }

    public function getNomPrenom(): ?string
    {
        return $this->nomPrenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setOrdre(?int $ordre): self
    {
        $this->ordre = $ordre;
        return $this;
    }

    public function getOrdre(): ?int
    {
        return $this->ordre;
    }

    public function setIsPrincipal(bool $isPrincipal): self
    {
        $this->isPrincipal = $isPrincipal;
        return $this;
    }

    public function getIsPrincipal(): ?bool
    {
        return $this->isPrincipal;
    }

    public function getIsPersonneMorale(): ?bool
    {
        return $this->isPersonneMorale;
    }

    public function setIspersonneMorale(bool $isPersonneMorale): self
    {
        $this->isPersonneMorale = $isPersonneMorale;
        return $this;
    }

    public function setNumeroNotice(?string $numeroNotice): self
    {
        $this->numeroNotice = $numeroNotice;
        return $this;
    }

    public function getNumeroNotice(): ?string
    {
        return $this->numeroNotice;
    }


    public function setDateNaissanceDeces(?string $dateNaissanceDeces): self
    {
        $this->dateNaissanceDeces = $dateNaissanceDeces;
        return $this;
    }

    public function getDateNaissanceDeces(): ?string
    {
        return $this->dateNaissanceDeces;
    }

    public function setQualification(?string $qualification): self
    {
        $this->qualification = $qualification;
        return $this;
    }

    public function getQualification(): ?string
    {
        return $this->qualification;
    }

    public function setPrixNobel(?PrixNobel $prixNobel): self
    {
        $this->prixNobel = $prixNobel;
        return $this;
    }

    public function getPrixNobel(): ?prixNobel
    {
        return $this->prixNobel;
    }

    public function setNationalite(?Nationalite $nationalite): self
    {
        $this->nationalite = $nationalite;
        return $this;
    }

    public function getNationalite(): ?Nationalite
    {
        return $this->nationalite;
    }

    public function setIsBnf(?bool $isBnf): self
    {
        $this->isBnf = $isBnf;
        return $this;
    }

    public function getIsBnf(): ?bool
    {
        return $this->isBnf;
    }

    public function setBiographie(?string $biographie): self
    {
        $this->biographie = $biographie;
        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function hasPrixNobel(): bool
    {
        return $this->prixNobel !== null;
    }

    public function getCodeOperateurDerniereModification(): ?string
    {
        return $this->codeOperateurDerniereModification;
    }

    public function setCodeOperateurDerniereModification(?string $codeOperateur): self
    {
        $this->codeOperateurDerniereModification = $codeOperateur;
        return $this;
    }

    public function getCodeSiteDerniereModification(): ?string
    {
        return $this->codeSiteDerniereModification;
    }

    public function setCodeSiteDerniereModification(?string $codeSite): self
    {
        $this->codeSiteDerniereModification = $codeSite;
        return $this;
    }

    public function getHeureDerniereModification(): ?\DateTime
    {
        return $this->heureDerniereModification;
    }

    public function setHeureDerniereModification(?\DateTime $heureDerniereModification): self
    {
        $this->heureDerniereModification = $heureDerniereModification;
        return $this;
    }

    public function setIsBiographieEnrichie(?bool $isBiographieEnrichie): self
    {
        $this->isBiographieEnrichie = $isBiographieEnrichie;
        return $this;
    }

    public function getIsBiographieEnrichie(): ?bool
    {
        return $this->isBiographieEnrichie;
    }

    public function setUpdatedAt(?string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setIsBloque(?bool $isBloque): self
    {
        $this->isBloque = $isBloque;
        return $this;
    }

    public function getIsBloque(): ?bool
    {
        return $this->isBloque;
    }
}
