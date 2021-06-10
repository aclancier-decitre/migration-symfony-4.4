<?php

namespace App\Entity\AppEntity;

class Site implements \JsonSerializable
{

    private string $code;

    private string $name;

    private bool $isEphemeral;

    private bool $isStore;

    public function __construct(string $code, string $name, bool $isEphemeral, bool $isStore)
    {
        $this->code = $code;
        $this->name = $name;
        $this->isEphemeral = $isEphemeral;
        $this->isStore = $isStore;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isEphemeral(): ?bool
    {
        return $this->isEphemeral;
    }

    public function setIsEphemeral(bool $isEphemeral): self
    {
        $this->isEphemeral = $isEphemeral;
        return $this;
    }

    public function isStore(): ?bool
    {
        return $this->isStore;
    }

    public function setIsStore(bool $isStore): self
    {
        $this->isStore = $isStore;
        return $this;
    }

    public function jsonSerialize()
    {
        return [
            'code' => $this->code,
            'name' => $this->name,
            'is_ephemeral' => $this->isEphemeral,
            'is_store' => $this->isStore
        ];
    }
}
