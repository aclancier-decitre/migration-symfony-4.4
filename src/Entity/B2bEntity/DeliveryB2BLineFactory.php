<?php

namespace App\Entity\B2bEntity;

class DeliveryB2BLineFactory
{

    /**
     * @param array $deliveryB2BLineInfos
     * @return DeliveryB2BLine
     */
    public function createFromArray(array $deliveryB2BLineInfos)
    {
        $deliveryB2bLine = new DeliveryB2BLine();
        $deliveryB2bLine
            ->setProductCode($deliveryB2BLineInfos["code_produit"] ?? null)
            ->setLineNumber($deliveryB2BLineInfos["numero_de_ligne"])
            ->setProductTitle($deliveryB2BLineInfos["titre_produit"] ?? null)
            ->setDeliveredQuantity($deliveryB2BLineInfos["quantite_livree"]);

        return $deliveryB2bLine;
    }
}
