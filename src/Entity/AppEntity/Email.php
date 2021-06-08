<?php

namespace App\Entity\AppEntity;

class Email
{
    /** @var string */
    private $id;

    /** @var string */
    private $address;

    /** @var bool */
    private $npai;

    /** @var string */
    private $typeCode;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function isNpai(): ?bool
    {
        return $this->npai;
    }

    public function setNpai(?bool $npai): self
    {
        $this->npai = $npai;
        return $this;
    }

    public function getTypeCode(): ?string
    {
        return $this->typeCode;
    }

    public function setTypeCode(?string $typeCode): self
    {
        $this->typeCode = $typeCode;
        return $this;
    }
}
