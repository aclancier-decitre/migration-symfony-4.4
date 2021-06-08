<?php

namespace App\Entity\B2bEntity;

class DeliveryB2B
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var DeliveryB2BLine[]
     */
    private $products;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function __clone()
    {
        for ($i = 0; $i < count($this->products); $i++) {
            $this->products[$i] = clone $this->products[$i];
        }
    }

    /**
     * @return DeliveryB2BLine[]|null
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param $products
     * @return $this
     */
    public function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @param integer $lineNumber
     * @return DeliveryB2BLine|null
     */
    public function getProductByLineNumber($lineNumber)
    {

        foreach ($this->products as $product) {
            if ($product->getLineNumber() == $lineNumber) {
                return $product;
            }
        }

        return null;
    }
}
