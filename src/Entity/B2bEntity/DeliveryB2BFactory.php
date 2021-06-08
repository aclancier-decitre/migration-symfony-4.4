<?php

namespace App\Entity\B2bEntity;

class DeliveryB2BFactory
{

    /**
     * @param array $deliveryB2BInfos
     * @return DeliveryB2B
     */
    public function createFromArray(array $deliveryB2BInfos)
    {

        $products = [];
        $productLine = new DeliveryB2BLineFactory();

        foreach ($deliveryB2BInfos["products"] as $product) {
            $products[] = $productLine->createFromArray($product);
        }

        $deliveryB2b = new DeliveryB2B();
        $deliveryB2b
            ->setId($deliveryB2BInfos["id"])
            ->setProducts($products);

        return $deliveryB2b;
    }
}
