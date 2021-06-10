<?php

namespace App\Entity\StockEntity;

use App\Entity\AppEntity\Site;

class StockLine
{

    private Site $site;

    private bool $isProductUsed;

    private StockType $stockType;

    private int $quantity;

    public function __construct(Site $site, bool $isProductUsed, StockType $stockType, int $quantity)
    {
        $this->site = $site;
        $this->isProductUsed = $isProductUsed;
        $this->stockType = $stockType;
        $this->quantity = $quantity;
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

    public function isProductUsed(): bool
    {
        return $this->isProductUsed;
    }

    public function setIsProductUsed(bool $isProductUsed): self
    {
        $this->isProductUsed = $isProductUsed;
        return $this;
    }

    public function getStockType(): StockType
    {
        return $this->stockType;
    }

    public function setStockType(StockType $stockType): self
    {
        $this->stockType = $stockType;
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
}
