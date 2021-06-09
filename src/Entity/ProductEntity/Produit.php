<?php

namespace App\Entity\ProductEntity;

use App\Entity\ProductEntity\Auteur;
use App\Entity\ProductEntity\Editeur;

class Produit
{
    private string $ean;

    private string $codeTypeProduit;

    private bool $isPrecre;

    private bool $isAParaitre;

    private bool $isReference;

    private bool $commandeLnBloquee = false;

    private bool $isGestionStockCentral;

    private ?int $quantiteEnStock;

    private ?bool $isPrixFixeDecitre;

    private ?string $libelle = null;

    private ?string $codeFamille = null;

    private ?string $codeSousFamille = null;

    private ?string $codeSousSousFamille = null;

    private ?float $prixTtc = null;

    private ?float $prixHt = null;

    private ?float $tauxTva = null;

    private ?string $codeTva = null;

    private ?string $libelleTva = null;

    private ?string $codeDispo = null;

    private ?string $codeFournisseur = null;

    private ?string $codeProduitFournisseur = null;

    private ?string $nomFournisseur = null;

    private ?string $codeModeTransmission = null;

    private ?string $libelleModeTransmission = null;

    private ?\DateTime $dateAnnulation = null;

    private ?\DateTime $dateParution = null;

    private ?\DateTime $dateDebutBloquage = null;

    private ?\DateTime $dateCreation = null;

    private ?array $donneesProduitLn = null;

    private ?int $quantiteConditionAchat = null;

    private ?Editeur $editeur = null;

    private ?Auteur $auteurPrincipal = null;

    public function __construct(
        string $ean,
        string $codeTypeProduit,
        bool $isPrecre,
        bool $isAParaitre,
        bool $isReference,
        bool $commandeLnBloquee,
        bool $isGestionStockCentral,
        ?int $quantiteEnStock,
        ?bool $isPrixFixeDecitre,
        ?string $libelle,
        ?string $codeFamille,
        ?string $codeSousFamille,
        ?string $codeSousSousFamille,
        ?float $prixTtc,
        ?float $prixHt,
        ?float $tauxTva,
        ?string $codeTva,
        ?string $libelleTva,
        ?string $codeDispo,
        ?string $codeFournisseur,
        ?string $codeProduitFournisseur,
        ?string $nomFournisseur,
        ?Editeur $editeur,
        ?Auteur $auteurPrincipal,
        ?string $codeModeTransmission,
        ?string $libelleModeTransmission,
        ?\DateTime $dateAnnulation,
        ?\DateTime $dateParution,
        ?\DateTime $dateDebutBloquage,
        ?\DateTime $dateCreation,
        ?int $quantiteConditionAchat
    ) {
        $this->ean = $ean;
        $this->codeTypeProduit = $codeTypeProduit;
        $this->isPrecre = $isPrecre;
        $this->isAParaitre = $isAParaitre;
        $this->isReference = $isReference;
        $this->commandeLnBloquee = $commandeLnBloquee;
        $this->isPrixFixeDecitre = $isPrixFixeDecitre;
        $this->libelle = $libelle;
        $this->codeFamille = $codeFamille;
        $this->codeSousFamille = $codeSousFamille;
        $this->codeSousSousFamille = $codeSousSousFamille;
        $this->prixTtc = $prixTtc;
        $this->prixHt = $prixHt;
        $this->tauxTva = $tauxTva;
        $this->codeTva = $codeTva;
        $this->libelleTva = $libelleTva;
        $this->codeDispo = $codeDispo;
        $this->codeFournisseur = $codeFournisseur;
        $this->codeProduitFournisseur = $codeProduitFournisseur;
        $this->nomFournisseur = $nomFournisseur;
        $this->editeur = $editeur;
        $this->auteurPrincipal = $auteurPrincipal;
        $this->codeModeTransmission = $codeModeTransmission;
        $this->libelleModeTransmission = $libelleModeTransmission;
        $this->dateAnnulation = $dateAnnulation;
        $this->dateParution = $dateParution;
        $this->dateDebutBloquage = $dateDebutBloquage;
        $this->dateCreation = $dateCreation;
        $this->quantiteEnStock = $quantiteEnStock;
        $this->quantiteConditionAchat = $quantiteConditionAchat;
        $this->isGestionStockCentral = $isGestionStockCentral;
    }

    /**
     * @param DonneesProduitLn[] $donneesProduitLn
     */
    public function setDonneesProduitLn(array $donneesProduitLn): self
    {
        $this->donneesProduitLn = $donneesProduitLn;
        return $this;
    }

    /**
     * @return DonneesProduitLn[]
     */
    public function getDonneesProduitLn(): array
    {
        return $this->donneesProduitLn;
    }

    public function getPrixTtc(): ?float
    {
        return $this->prixTtc;
    }

    public function getPrixHt(): ?float
    {
        return $this->prixHt;
    }

    public function setAuteurPrincipal(?Auteur $auteurPrincipal): self
    {
        $this->auteurPrincipal = $auteurPrincipal;
        return $this;
    }

    public function getAuteurPrincipal(): ?Auteur
    {
        return $this->auteurPrincipal;
    }

    public function setEditeur(?Editeur $editeur): self
    {
        $this->editeur = $editeur;
        return $this;
    }

    public function getEditeur(): ?Editeur
    {
        return $this->editeur;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;
        return $this;
    }

    public function getNomFournisseur(): ?string
    {
        return $this->nomFournisseur;
    }
}
