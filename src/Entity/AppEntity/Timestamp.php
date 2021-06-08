<?php

namespace App\Entity\AppEntity;

class Timestamp
{
    /** @var string */
    private $lastUpdateOperatorCode;

    /** @var \DateTime */
    private $lastUpdateByOperatorDate;

    /** @var string */
    private $lastUpdateSiteCode;

    /** @var string */
    private $lastUpdateTimestamp;

    public function __construct(
        string $lastUpdateOperatorCode = null,
        \DateTime $lastUpdateByOperatorDate = null,
        string $lastUpdateSiteCode = null,
        string $lastUpdateTimestamp = null
    ) {
        $this->lastUpdateOperatorCode = $lastUpdateOperatorCode;
        $this->lastUpdateByOperatorDate = $lastUpdateByOperatorDate;
        $this->lastUpdateSiteCode = $lastUpdateSiteCode;
        $this->lastUpdateTimestamp = $lastUpdateTimestamp;
    }

    public function getLastUpdateOperatorCode(): ?string
    {
        return $this->lastUpdateOperatorCode;
    }

    public function setLastUpdateOperatorCode(?string $lastUpdateOperatorCode): self
    {
        $this->lastUpdateOperatorCode = $lastUpdateOperatorCode;
        return $this;
    }

    public function getLastUpdateByOperatorDate(): ?\DateTime
    {
        return $this->lastUpdateByOperatorDate;
    }

    public function setLastUpdateByOperatorDate(?\DateTime $lastUpdateByOperatorDate): self
    {
        $this->lastUpdateByOperatorDate = $lastUpdateByOperatorDate;
        return $this;
    }

    public function getLastUpdateSiteCode(): ?string
    {
        return $this->lastUpdateSiteCode;
    }

    public function setLastUpdateSiteCode(?string $lastUpdateSiteCode): self
    {
        $this->lastUpdateSiteCode = $lastUpdateSiteCode;
        return $this;
    }

    // Timestamp (format VARCHAR(18)) de la dernière mise à jour.
    public function getLastUpdateTimestamp(): ?string
    {
        return $this->lastUpdateTimestamp;
    }

    public function setLastUpdateTimestamp(?string $lastUpdateTimestamp): self
    {
        $this->lastUpdateTimestamp = $lastUpdateTimestamp;
        return $this;
    }
}
