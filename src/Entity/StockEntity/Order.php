<?php

namespace App\Entity\StockEntity;

use App\Entity\AppEntity\Site;
use DateTime;

class Order
{

    private Site $site;

    private ?string $origin;

    private string $number;

    private ?DateTime $orderedAt;

    public function __construct(Site $site, string $number, string $origin = null, DateTime $orderedAt = null)
    {
        $this->site = $site;
        $this->origin = $origin;
        $this->number = $number;
        $this->orderedAt = $orderedAt;
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

    public function getOrigin(): ?string
    {
        return $this->origin;
    }

    public function setOrigin(string $origin): self
    {
        $this->origin = $origin;
        return $this;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getOrderedAt(): ?DateTime
    {
        return $this->orderedAt;
    }

    public function setOrderedAt(DateTime $orderedAt): self
    {
        $this->orderedAt = $orderedAt;
        return $this;
    }
}
