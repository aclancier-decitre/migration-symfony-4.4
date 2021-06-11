<?php

namespace App\Entity\CoreEntity;

use DateTime;

class Product
{

    private string $ean;

    private string $libelle;

    private string $codeFamille;

    private string $codeSousFamille;

    private string $codeSousSousFamille;

    private string $referenceFournisseur;

    private float $prixAchatUnitaire;

    private float $prixDeVente;

    private float $remise;

    private string $typeProduit;

    private ?Datetime $dateParution;

    private string $codeEditeur;

    private string $nomCollection;

    private string $nomEditeur;

    private string $adresseCouverture;

    private string $resume;

    private int $quantitePreco;

    /**
     * @return string
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @param $ean
     * @return $this
     */
    public function setEan($ean)
    {
        $this->ean = $ean;
        return $this;
    }

    /**
     * @return string
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param $libelle
     * @return $this
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodeFamille()
    {
        return $this->codeFamille;
    }

    /**
     * @param $codeFamille
     * @return $this
     */
    public function setCodeFamille($codeFamille)
    {
        $this->codeFamille = $codeFamille;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodeSousFamille()
    {
        return $this->codeSousFamille;
    }

    /**
     * @param $codeSousFamille
     * @return $this
     */
    public function setCodeSousFamille($codeSousFamille)
    {
        $this->codeSousFamille = $codeSousFamille;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodeSousSousFamille()
    {
        return $this->codeSousSousFamille;
    }

    /**
     * @param $codeSousSousFamille
     * @return $this
     */
    public function setCodeSousSousFamille($codeSousSousFamille)
    {
        $this->codeSousSousFamille = $codeSousSousFamille;
        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceFournisseur()
    {
        return $this->referenceFournisseur;
    }

    /**
     * @param $referenceFournisseur
     * @return $this
     */
    public function setReferenceFournisseur($referenceFournisseur)
    {
        $this->referenceFournisseur = $referenceFournisseur;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrixAchatUnitaire()
    {
        return $this->prixAchatUnitaire;
    }

    /**
     * @param $prixAchatUnitaire
     * @return $this
     */
    public function setPrixAchatUnitaire($prixAchatUnitaire)
    {
        $this->prixAchatUnitaire = $prixAchatUnitaire;
        return $this;
    }

    /**
     * @return float
     */
    public function getPrixDeVente()
    {
        return $this->prixDeVente;
    }

    /**
     * @param $prixDeVente
     * @return $this
     */
    public function setPrixDeVente($prixDeVente)
    {
        $this->prixDeVente = $prixDeVente;
        return $this;
    }

    /**
     * @return float
     */
    public function getRemise()
    {
        return $this->remise;
    }

    /**
     * @param $remise
     * @return $this
     */
    public function setRemise($remise)
    {
        $this->remise = $remise;
        return $this;
    }

    /**
     * @return string
     */
    public function getTypeProduit()
    {
        return $this->typeProduit;
    }

    /**
     * @param $typeProduit
     * @return $this
     */
    public function setTypeProduit($typeProduit)
    {
        $this->typeProduit = $typeProduit;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateParution()
    {
        return $this->dateParution;
    }

    /**
     * @param ?DateTime $dateParution
     * @return $this
     */
    public function setDateParution(?DateTime $dateParution)
    {
        $this->dateParution = $dateParution;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodeEditeur()
    {
        return $this->codeEditeur;
    }

    /**
     * @param string $codeEditeur
     * @return $this
     */
    public function setCodeEditeur($codeEditeur)
    {
        $this->codeEditeur = $codeEditeur;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomCollection()
    {
        return $this->nomCollection;
    }

    /**
     * @param string $nomCollection
     * @return $this
     */
    public function setNomCollection($nomCollection)
    {
        $this->nomCollection = $nomCollection;
        return $this;
    }

    /**
     * @return string
     */
    public function getNomEditeur()
    {
        return $this->nomEditeur;
    }

    /**
     * @param string $nomEditeur
     * @return $this
     */
    public function setNomEditeur($nomEditeur)
    {
        $this->nomEditeur = $nomEditeur;
        return $this;
    }

    /**
     * @return string
     */
    public function getAdresseCouverture()
    {
        return $this->adresseCouverture;
    }

    /**
     * @param string $adresseCouverture
     * @return $this
     */
    public function setAdresseCouverture($adresseCouverture)
    {
        $this->adresseCouverture = $adresseCouverture;
        return $this;
    }

    /**
     * @return string
     */
    public function getResume()
    {
        return $this->resume;
    }

    /**
     * @param string $resume
     * @return $this
     */
    public function setResume($resume)
    {
        $this->resume = $resume;
        return $this;
    }

    /**
     * @return integer
     */
    public function getQuantitePreco()
    {
        return $this->quantitePreco;
    }

    /**
     * @param integer $quantitePreco
     * @return $this
     */
    public function setQuantitePreco($quantitePreco)
    {
        $this->quantitePreco= $quantitePreco;
        return $this;
    }
}
