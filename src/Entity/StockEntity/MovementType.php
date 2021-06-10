<?php

namespace App\Entity\StockEntity;

class MovementType
{

    private string $code;

    private ?PhysicalFlowType $physicalFlowType;

    private string $label;

    private ?bool $isStockCorrection;

    private ?int $saleStatus;

    public function __construct(
        string $code,
        ?PhysicalFlowType $physicalFlowType,
        string $label,
        bool $isStockCorrection = null,
        int $saleStatus = null
    ) {
        $this->code = $code;
        $this->physicalFlowType = $physicalFlowType;
        $this->label = $label;
        $this->isStockCorrection = $isStockCorrection;
        $this->saleStatus = $saleStatus;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function getPhysicalFlowType(): ?PhysicalFlowType
    {
        return $this->physicalFlowType;
    }

    public function setPhysicalFlowType(PhysicalFlowType $physicalFlowType): self
    {
        $this->physicalFlowType = $physicalFlowType;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function isStockCorrection(): ?bool
    {
        return $this->isStockCorrection;
    }

    public function setIsStockCorrection(bool $isStockCorrection): self
    {
        $this->isStockCorrection = $isStockCorrection;
        return $this;
    }

    public function getSaleStatus(): ?int
    {
        return $this->saleStatus;
    }

    public function setSaleStatus(int $saleStatus): self
    {
        $this->saleStatus = $saleStatus;
        return $this;
    }

    public function isSale(): bool
    {
        return $this->saleStatus === 1;
    }
}
