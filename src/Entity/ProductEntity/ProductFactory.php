<?php

namespace App\Entity\ProductEntity;

class ProductFactory
{
    /**
     * @param array $productInfo
     * @return Product
     */
    public function createFromArray(array $productInfo)
    {
        $product = new Product();
        $product->setEan($productInfo["ean"])
            ->setLibelle($productInfo['libelle'])
            ->setCodeFamille($productInfo['code_famille'])
            ->setCodeSousFamille($productInfo['code_sous_famille'])
            ->setCodeSousSousFamille($productInfo['code_sous_sous_famille'])
            ->setReferenceFournisseur(
                isset($productInfo['reference_fournisseur']) ? $productInfo['reference_fournisseur'] : null
            )
            ->setPrixAchatUnitaire($productInfo['prix_achat_unitaire'])
            ->setPrixDeVente($productInfo['prix_de_vente'])
            ->setRemise($productInfo["remise"])
            ->setTypeProduit($productInfo["type_produit"])
            ->setDateParution(
                isset($productInfo["date_parution"]) ? new \DateTime($productInfo["date_parution"]) : null
            )
            ->setCodeEditeur($productInfo["code_editeur"])
            ->setNomCollection($productInfo["nom_collection"]?:null)
            ->setNomEditeur($productInfo["nom_editeur"]?:null)
            ->setAdresseCouverture($productInfo["cover_url"]?:null)
            ->setResume($productInfo["resume"]?:null)
            ->setQuantitePreco($productInfo["quantite_preco"]?:null);

        return $product;
    }
}
