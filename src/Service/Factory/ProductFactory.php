<?php

namespace App\Service\Factory;

use App\Service\Tools\DatabaseDateHelper;
use App\Entity\ProductEntity\Product;

class ProductFactory
{
    public function createFromArrayDetails(array $data): Product
    {
        $product = new Product();

        $product->setEan($data['ean'])
            ->setTitle($data['titre'])
            ->setDateAnnulation($data['dtann'])
            ->setIsPrecree($data['cdprecre'] === 1)
            ->setNombrePages($data['nombre_pages'])
            ->setCodeFamille($data['code_famille'] ?? null)
            ->setLibelleFamille($data['libelle_famille'] ?? null)
            ->setCodeSousFamille($data['code_sous_famille'] ?? null)
            ->setLibelleSousFamille($data['libelle_sous_famille'] ?? null)
            ->setCodeSousSousFamille($data['code_sous_sous_famille'] ?? null)
            ->setLibelleSousSousFamille($data['libelle_sous_sous_famille'] ?? null)
            ->setBiographie($data['biographie'] ?? null)
            ->setResume($data['resume'] ?? null)
            ->setIsbn($data['isbn'] ?? null)
            ->setDateParution(DatabaseDateHelper::getDateFromString($data['date_parution'] ?? null))
            ->setPriceAllTaxesIncluded($data['prix_ttc'] ?? null)
            ->setLibelleTauxTva($data['libelle_taux_tva'] ?? null)
            ->setNomFournisseur($data['nom_fournisseur'] ?? null)
            ->setCodeFournisseur($data['code_fournisseur'] ?? null)
            ->setNomEditeur($data['nom_editeur'] ?? null)
            ->setCodeEditeur($data['code_editeur'] ?? null)
            ->setLibelleSerie($data['libelle_serie'] ?? null)
            ->setLibelleCollection($data['libelle_collection'] ?? null)
            ->setLibelleEdition($data['libelle_edition'] ?? null)
            ->setLibelleMarqueEditioriale($data['libelle_marque_editoriale'] ?? null)
            ->setLibelleFormat($data['libelle_format'] ?? null)
            ->setLibellePresentation($data['libelle_presentation'] ?? null)
            ->setLargeur($data['largeur'] ?? null)
            ->setHauteur($data['hauteur'] ?? null)
            ->setEpaisseur($data['epaisseur'] ?? null)
            ->setPoids($data['poids'] ?? null)
            ->setLibelleDisponibiliteDilicom($data['libelle_disponibilite_dilicom'] ?? null)
            ->setDateMajDisponibiliteDilicom(DatabaseDateHelper::getDateFromString(
                $data['date_maj_disponibilite_dilicom'] ?? null
            ))
            ->setLibelleDisponibiliteFournisseur($data['libelle_disponibilite_fournisseur'])
            ->setDateMajDisponibiliteFournisseur(DatabaseDateHelper::getDateFromString(
                $data['date_maj_disponibilite_fournisseur'] ?? null
            ));

        if (($data['auteur'] ?? null) !== null) {
            $auteur = json_decode($data['auteur']);

            if ($auteur !== false) {
                usort($auteur, function ($auteur1, $auteur2) {
                    return $auteur1->ordre <=> $auteur2->ordre;
                });

                $product->setAuteur($auteur);
            }
        }


        return $product;
    }
}
