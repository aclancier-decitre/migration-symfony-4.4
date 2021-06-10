<?php

namespace App\Entity\StockEntity;

use DateTime;

class OrderLine
{

    private Order $order;

    private Order $supplierOrder;

    private int $orderedQuantity;

    private int $receivedQuantity;

    private int $underwayQuantity;

    private ?DateTime $plannedDeliveryDate;

    private ?string $label;

    public function __construct(
        Order $order,
        Order $supplierOrder,
        int $orderedQuantity,
        int $receivedQuantity,
        int $underwayQuantity,
        ?DateTime $plannedDeliveryDate,
        string $label = null
    ) {
        $this->order = $order;
        $this->supplierOrder = $supplierOrder;
        $this->orderedQuantity = $orderedQuantity;
        $this->receivedQuantity = $receivedQuantity;
        $this->underwayQuantity = $underwayQuantity;
        $this->plannedDeliveryDate = $plannedDeliveryDate;
        $this->label = $label;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function setOrder(Order $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getSupplierOrder(): Order
    {
        return $this->supplierOrder;
    }

    public function setSupplierOrder(Order $supplierOrder): self
    {
        $this->supplierOrder = $supplierOrder;
        return $this;
    }

    public function getOrderedQuantity(): int
    {
        return $this->orderedQuantity;
    }

    public function setOrderedQuantity(int $orderedQuantity): self
    {
        $this->orderedQuantity = $orderedQuantity;
        return $this;
    }

    public function getReceivedQuantity(): int
    {
        return $this->receivedQuantity;
    }

    public function setReceivedQuantity(int $receivedQuantity): self
    {
        $this->receivedQuantity = $receivedQuantity;
        return $this;
    }

    public function getUnderwayQuantity(): int
    {
        return $this->underwayQuantity;
    }

    public function setUnderwayQuantity(int $underwayQuantity): self
    {
        $this->underwayQuantity = $underwayQuantity;
        return $this;
    }

    public function getPlannedDeliveryDate(): ?DateTime
    {
        return $this->plannedDeliveryDate;
    }

    public function setPlannedDeliveryDate(?DateTime $plannedDeliveryDate): self
    {
        $this->plannedDeliveryDate = $plannedDeliveryDate;
        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }
}
