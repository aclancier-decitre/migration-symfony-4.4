<?php

namespace App\Entity\B2bEntity;

class DeliveryB2BLine
{

    /**
     * @var string
     */
    private $productCode;

    /**
     * @var string
     */
    private $productTitle;

    /**
     * @var integer
     */
    private $deliveredQuantity;

    /**
     * @var integer
     */
    private $lineNumber;

    /**
     * @return int
     */
    public function getLineNumber(): int
    {
        return $this->lineNumber;
    }

    /**
     * @param int $lineNumber
     * @return $this
     */
    public function setLineNumber($lineNumber)
    {
        $this->lineNumber = $lineNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductCode()
    {
        return $this->productCode;
    }

    /**
     * @param string $productCode
     * @return $this
     */
    public function setProductCode($productCode)
    {
        $this->productCode = $productCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getProductTitle()
    {
        return $this->productTitle;
    }

    /**
     * @param string $productTitle
     * @return $this
     */
    public function setProductTitle($productTitle)
    {
        $this->productTitle = $productTitle;
        return $this;
    }

    /**
     * @return int
     */
    public function getDeliveredQuantity()
    {
        return $this->deliveredQuantity;
    }

    /**
     * @param int $deliveredQuantity
     * @return $this
     */
    public function setDeliveredQuantity($deliveredQuantity)
    {
        $this->deliveredQuantity = $deliveredQuantity;
        return $this;
    }
}
