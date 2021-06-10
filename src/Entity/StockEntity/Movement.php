<?php

namespace App\Entity\StockEntity;

use App\Entity\AppEntity\Site;
use App\Entity\AppEntity\Timestamp;
use DateTime;

class Movement
{

    private Site $site;

    private DateTime $date;

    private MovementType $movementType;

    private PhysicalFlowType $physicalFlowType;

    private int $quantity;

    private ?string $origin;

    private ?string $reference;

    private float $priceWithoutTaxes;

    private ?StockType $stockTypeOrigin;

    private ?StockType $stockTypeDestination;

    private ?string $supplierInvoiceNumber;

    private bool $isUsedProduct;

    private Timestamp $timestamp;

    public function __construct(
        Site $site,
        \DateTime $date,
        PhysicalFlowType $physicalFlowType,
        MovementType $movementType,
        int $quantity,
        float $priceWithoutTaxes,
        bool $isUsedProduct,
        Timestamp $timestamp,
        string $reference = null,
        string $origin = null,
        string $supplierInvoiceNumber = null,
        StockType $stockTypeOrigin = null,
        StockType $stockTypeDestination = null
    ) {
        $this->site = $site;
        $this->date = $date;
        $this->physicalFlowType = $physicalFlowType;
        $this->movementType = $movementType;
        $this->quantity = $quantity;
        $this->priceWithoutTaxes = $priceWithoutTaxes;
        $this->stockTypeOrigin = $stockTypeOrigin;
        $this->stockTypeDestination = $stockTypeDestination;
        $this->supplierInvoiceNumber = $supplierInvoiceNumber;
        $this->timestamp = $timestamp;
        $this->origin = $origin;
        $this->reference = $reference;
        $this->isUsedProduct = $isUsedProduct;
    }

    public function getSite(): Site
    {
        return $this->site;
    }

    public function setSite(Site $site): self
    {
        $this->site = $site;
        return $this;
    }

    public function getDate(): \DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

    public function getMovementType(): MovementType
    {
        return $this->movementType;
    }

    public function setMovementType(MovementType $movementType): self
    {
        $this->movementType = $movementType;
        return $this;
    }

    public function getPhysicalFlowType(): PhysicalFlowType
    {
        return $this->physicalFlowType;
    }

    public function setPhysicalFlowType(PhysicalFlowType $physicalFlowType): self
    {
        $this->physicalFlowType = $physicalFlowType;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getPriceWithoutTaxes(): float
    {
        return $this->priceWithoutTaxes;
    }

    public function setPriceWithoutTaxes(float $priceWithoutTaxes): self
    {
        $this->priceWithoutTaxes = $priceWithoutTaxes;
        return $this;
    }

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): self
    {
        $this->origin = $origin;
        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    public function getStockTypeOrigin(): ?StockType
    {
        return $this->stockTypeOrigin;
    }

    public function setStockTypeOrigin(StockType $stockTypeOrigin): self
    {
        $this->stockTypeOrigin = $stockTypeOrigin;
        return $this;
    }

    public function getStockTypeDestination(): ?StockType
    {
        return $this->stockTypeDestination;
    }

    public function setStockTypeDestination(StockType $stockTypeDestination): self
    {
        $this->stockTypeDestination = $stockTypeDestination;
        return $this;
    }

    public function getSupplierInvoiceNumber(): ?string
    {
        return $this->supplierInvoiceNumber;
    }

    public function setSupplierInvoiceNumber(string $supplierInvoiceNumber): self
    {
        $this->supplierInvoiceNumber = $supplierInvoiceNumber;
        return $this;
    }

    public function isUsedProduct(): bool
    {
        return $this->isUsedProduct;
    }

    public function setIsUsedProduct(bool $isUsedProduct): self
    {
        $this->isUsedProduct = $isUsedProduct;
        return $this;
    }

    public function getTimestamp(): Timestamp
    {
        return $this->timestamp;
    }

    public function setTimestamp(Timestamp $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }
}
