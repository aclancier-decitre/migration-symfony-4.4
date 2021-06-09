<?php

namespace App\Entity\ProductEntity;

class Product
{

    /**
     * @var string
     */
    private $ean;

    /**
     * @var string|null
     */
    private $title;

    /**
     * @var float|null
     */
    private $price_all_taxes_included;


    private ?\DateTime $dateAnnulation = null;

    private bool $isPrecree = false;

    /**
     * @var Auteur[]
     */
    private array $auteurs;

    private ?string $codeFamille = null;
    private ?string $codeSousFamille = null;
    private ?string $codeSousSousFamille = null;

    private ?string $libelleFamille = null;
    private ?string $libelleSousFamille = null;
    private ?string $libelleSousSousFamille = null;

    private ?string $resume = null;

    private ?string $biographie = null;

    private ?string $isbn = null;

    private ?\DateTime $dateParution = null;

    private ?string $libelleTauxTva = null;

    private ?string $nomFournisseur = null;

    private ?string $codeFournisseur = null;

    private ?string $nomEditeur = null;

    private ?string $codeEditeur = null;

    private ?string $libelleSerie = null;

    private ?string $libelleCollection = null;

    private ?string $libelleEdition = null;

    private ?string $libelleMarqueEditioriale = null;

    private ?string $libelleFormat = null;

    private ?int $nombrePages = null;

    private ?string $libellePresentation = null;

    private ?float $largeur = null;
    private ?float $hauteur = null;
    private ?float $epaisseur = null;

    private ?float $poids = null;

    private ?string $libelleDisponibiliteDilicom = null;
    private ?\DateTime $dateMajDisponibiliteDilicom = null;

    private ?string $libelleDisponibiliteFournisseur = null;
    private ?\DateTime $dateMajDisponibiliteFournisseur = null;

    /**
     * @return string
     */
    public function getEan(): string
    {
        return $this->ean;
    }

    /**
     * @param string $productCode
     * @return Product
     */
    public function setEan(string $productCode): self
    {
        $this->ean = $productCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Product
     */
    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return float
     */
    public function getPriceAllTaxesIncluded(): ?float
    {
        return $this->price_all_taxes_included;
    }

    /**
     * @param float $price_all_taxes_included
     * @return Product
     */
    public function setPriceAllTaxesIncluded(?float $price_all_taxes_included): self
    {
        $this->price_all_taxes_included = $price_all_taxes_included;
        return $this;
    }

    public function getDateAnnulation(): ?\DateTime
    {
        return $this->dateAnnulation;
    }

    public function setDateAnnulation(?string $dateAnnulation): self
    {
        if ($dateAnnulation instanceof \DateTime) {
            $this->dateAnnulation = $dateAnnulation;
        } elseif ($dateAnnulation !== null) {
            $this->dateAnnulation = new \DateTime($dateAnnulation);
        }
        return $this;
    }

    public function getIsPrecree(): bool
    {
        return $this->isPrecree;
    }

    public function setIsPrecree(bool $isPrecree) : self
    {
        $this->isPrecree = $isPrecree;
        return $this;
    }

    public function getAuteurs(): array
    {
        return $this->auteurs;
    }

    public function setAuteur(array $auteur) : self
    {
        $this->auteurs = $auteur;
        return $this;
    }

    public function getCodeFamille(): ?string
    {
        return $this->codeFamille;
    }

    public function setCodeFamille(?string $codeFamille): self
    {
        $this->codeFamille = $codeFamille;
        return $this;
    }

    public function getCodeSousFamille(): ?string
    {
        return $this->codeSousFamille;
    }

    public function setCodeSousFamille(?string $codeSousFamille): self
    {
        $this->codeSousFamille = $codeSousFamille;
        return $this;
    }

    public function getCodeSousSousFamille(): ?string
    {
        return $this->codeSousSousFamille;
    }

    public function setCodeSousSousFamille(?string $codeSousSousFamille): self
    {
        $this->codeSousSousFamille = $codeSousSousFamille;
        return $this;
    }

    public function getLibelleFamille(): ?string
    {
        return $this->libelleFamille;
    }

    public function setLibelleFamille(?string $libelleFamille): self
    {
        $this->libelleFamille = $libelleFamille;
        return $this;
    }

    public function getLibelleSousFamille(): ?string
    {
        return $this->libelleSousFamille;
    }

    public function setLibelleSousFamille(?string $libelleSousFamille): self
    {
        $this->libelleSousFamille = $libelleSousFamille;
        return $this;
    }

    public function getLibelleSousSousFamille(): ?string
    {
        return $this->libelleSousSousFamille;
    }

    public function setLibelleSousSousFamille(?string $libelleSousSousFamille): self
    {
        $this->libelleSousSousFamille = $libelleSousSousFamille;
        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): self
    {
        $this->resume = $resume;
        return $this;
    }

    public function getBiographie(): ?string
    {
        return $this->biographie;
    }

    public function setBiographie(?string $biographie): self
    {
        $this->biographie = $biographie;
        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;
        return $this;
    }

    public function getDateParution(): ?\DateTime
    {
        return $this->dateParution;
    }

    public function setDateParution(?\DateTime $dateParution): self
    {
        $this->dateParution = $dateParution;
        return $this;
    }

    public function getLibelleTauxTva(): ?string
    {
        return $this->libelleTauxTva;
    }

    public function setLibelleTauxTva(?string $libelleTauxTva): self
    {
        $this->libelleTauxTva = $libelleTauxTva;
        return $this;
    }

    public function getNomFournisseur(): ?string
    {
        return $this->nomFournisseur;
    }

    public function setNomFournisseur(?string $nomFournisseur): self
    {
        $this->nomFournisseur = $nomFournisseur;
        return $this;
    }

    public function getCodeFournisseur(): ?string
    {
        return $this->codeFournisseur;
    }

    public function setCodeFournisseur(?string $codeFournisseur): self
    {
        $this->codeFournisseur = $codeFournisseur;
        return $this;
    }

    public function getNomEditeur(): ?string
    {
        return $this->nomEditeur;
    }

    public function setNomEditeur(?string $nomEditeur): self
    {
        $this->nomEditeur = $nomEditeur;
        return $this;
    }

    public function getCodeEditeur(): ?string
    {
        return $this->codeEditeur;
    }

    public function setCodeEditeur(?string $codeEditeur): self
    {
        $this->codeEditeur = $codeEditeur;
        return $this;
    }

    public function getLibelleSerie(): ?string
    {
        return $this->libelleSerie;
    }

    public function setLibelleSerie(?string $libelleSerie): self
    {
        $this->libelleSerie = $libelleSerie;
        return $this;
    }

    public function getLibelleCollection(): ?string
    {
        return $this->libelleCollection;
    }

    public function setLibelleCollection(?string $libelleCollection): self
    {
        $this->libelleCollection = $libelleCollection;
        return $this;
    }

    public function getLibelleEdition(): ?string
    {
        return $this->libelleEdition;
    }

    public function setLibelleEdition(?string $libelleEdition): self
    {
        $this->libelleEdition = $libelleEdition;
        return $this;
    }

    public function getLibelleMarqueEditioriale(): ?string
    {
        return $this->libelleMarqueEditioriale;
    }

    public function setLibelleMarqueEditioriale(?string $libelleMarqueEditioriale): self
    {
        $this->libelleMarqueEditioriale = $libelleMarqueEditioriale;
        return $this;
    }

    public function getLibelleFormat(): ?string
    {
        return $this->libelleFormat;
    }

    public function setLibelleFormat(?string $libelleFormat): self
    {
        $this->libelleFormat = $libelleFormat;
        return $this;
    }

    public function getNombrePages(): ?int
    {
        return $this->nombrePages;
    }

    public function setNombrePages(?int $nombrePages): self
    {
        $this->nombrePages = $nombrePages;
        return $this;
    }

    public function getLibellePresentation(): ?string
    {
        return $this->libellePresentation;
    }

    public function setLibellePresentation(?string $libellePresentation): self
    {
        $this->libellePresentation = $libellePresentation;
        return $this;
    }

    public function getHauteur(): ?float
    {
        return $this->hauteur;
    }

    public function setHauteur(?float $hauteur): self
    {
        $this->hauteur = $hauteur;
        return $this;
    }

    public function getLargeur(): ?float
    {
        return $this->largeur;
    }

    public function setLargeur(?float $largeur): self
    {
        $this->largeur = $largeur;
        return $this;
    }

    public function getEpaisseur(): ?float
    {
        return $this->epaisseur;
    }

    public function setEpaisseur(?float $epaisseur): self
    {
        $this->epaisseur = $epaisseur;
        return $this;
    }

    public function getPoids(): ?float
    {
        return $this->poids;
    }

    public function setPoids(?float $poids): self
    {
        $this->poids = $poids;
        return $this;
    }

    public function getLibelleDisponibiliteDilicom(): ?string
    {
        return $this->libelleDisponibiliteDilicom;
    }

    public function setLibelleDisponibiliteDilicom(?string $libelleDisponibiliteDilicom): self
    {
        $this->libelleDisponibiliteDilicom = $libelleDisponibiliteDilicom;
        return $this;
    }

    public function getDateMajDisponibiliteDilicom(): ?\DateTime
    {
        return $this->dateMajDisponibiliteDilicom;
    }

    public function setDateMajDisponibiliteDilicom(?\DateTime $dateMajDisponibiliteDilicom): self
    {
        $this->dateMajDisponibiliteDilicom = $dateMajDisponibiliteDilicom;
        return $this;
    }

    public function getLibelleDisponibiliteFournisseur(): ?string
    {
        return $this->libelleDisponibiliteFournisseur;
    }

    public function setLibelleDisponibiliteFournisseur(?string $libelleDisponibiliteFournisseur): self
    {
        $this->libelleDisponibiliteFournisseur = $libelleDisponibiliteFournisseur;
        return $this;
    }

    public function getDateMajDisponibiliteFournisseur(): ?\DateTime
    {
        return $this->dateMajDisponibiliteFournisseur;
    }

    public function setDateMajDisponibiliteFournisseur(?\DateTime $dateMajDisponibiliteFournisseur): self
    {
        $this->dateMajDisponibiliteFournisseur = $dateMajDisponibiliteFournisseur;
        return $this;
    }

    public function isParu()
    {
        if (null === $this->dateParution) {
            return false;
        }

        return $this->dateParution < new \DateTime();
    }
}
