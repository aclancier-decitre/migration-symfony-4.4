<?php

namespace App\Entity\StockEntity;

use App\Entity\AppEntity\Site;

class RecapLine
{

    private Site $site;

    private int $quantity;

    private MovementType $movementType;

    public function __construct(Site $site, int $quantity, MovementType $movementType)
    {
        $this->site = $site;
        $this->quantity = $quantity;
        $this->movementType = $movementType;
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

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
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
}
