<?php

namespace App\Entity\StockEntity;

class ClientReservationLine
{

    private string $siteCode;

    private string $orderOriginLabel;

    private string $orderNumber;

    private string $clientId;

    private string $clientName;

    private int $quantity;

    public function __construct(
        string $siteCode,
        string $orderOriginLabel,
        string $orderNumber,
        string $clientId,
        string $clientName,
        int $quantity
    ) {
        $this->siteCode = $siteCode;
        $this->orderOriginLabel = $orderOriginLabel;
        $this->orderNumber = $orderNumber;
        $this->clientId = $clientId;
        $this->clientName = $clientName;
        $this->quantity = $quantity;
    }

    public function getSiteCode(): string
    {
        return $this->siteCode;
    }

    public function getOrderOriginLabel(): string
    {
        return $this->orderOriginLabel;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getClientName(): string
    {
        return $this->clientName;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }
}
