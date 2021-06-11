<?php

namespace App\Entity\RepriseEntity;

use App\Entity\ClientEntity\Client;

class Reprise
{

    private int $id;

    private Client $client;

    private array $livres;

    protected float $montantMaximum = 20;

    /**
     * @param Livre $livre
     *
     * @return Reprise
     */
    public function addLivre(Livre $livre)
    {
        $this->livres[] = $livre;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearLivres()
    {
        $this->livres = array();

        return $this;
    }

    /**
     * @return float
     */
    public function getMontant()
    {
        $montant = 0;

        foreach ($this->getLivres() as $livre) {
            $montant += $livre->getMontant();
        }

        return min($montant, $this->getMontantMaximum());
    }

    /**
     * @return bool
     */
    public function hasLivres()
    {
        return count($this->livres) > 0;
    }


    /**
     * @return bool
     */
    public function isValidable()
    {
        return $this->hasClient() && $this->hasLivres();
    }

    /**
     * @return bool
     */
    public function hasClient()
    {
        return null !== $this->getClient();
    }

    /**
     * @return int
     */
    public function getNbLivres()
    {
        return count($this->livres);
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Client $client
     *
     * @return Reprise
     */
    public function setClient(Client $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return Livre[]
     */
    public function getLivres()
    {
        return $this->livres;
    }

    /**
     * @param $montantMaximum
     * @return $this
     */
    public function setMontantMaximum($montantMaximum)
    {
        $this->montantMaximum = $montantMaximum;

        return $this;
    }

    /**
     * @return float
     */
    public function getMontantMaximum()
    {
        return $this->montantMaximum;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $infos = array(
            'client_id' => $this->getClient()->getId(),
            'produits'  => array(),
        );

        $montantReprise = 0;
        $montantMaximum = $this->getMontantMaximum();

        foreach ($this->getLivres() as $livre) {
            $isReprisDecitre = $livre->isReprisDecitre();
            $isReprisFonds   = $livre->isReprisFonds();

            if (!$isReprisDecitre && !$isReprisFonds) {
                continue;
            }

            // On a comme montant celui du livre au mieux ou sinon le reste de la reprise disponible
            $montantProduit = min($livre->getMontant(), $montantMaximum - $montantReprise);

            $montantReprise += $livre->getMontant();

            // Le montant de la reprise ne doit pas dépasser le montant maximum
            $montantReprise = min($montantReprise, $montantMaximum);

            $produit = array(
                'sku'         => $livre->getEan(),
                'montant'     => $montantProduit,
                'destination' => $isReprisDecitre ? 'decitre' : 'fonds',
            );

            $infos['produits'][] = $produit;
        }

        return $infos;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
