<?php

namespace App\Entity\AppEntity;

class Telephone
{
    // Fixe principal
    public const TYPE_LANDLINE_MAIN = '001';

    // Portable
    public const TYPE_MOBILE = '002';

    /** @var integer */
    private $id;

    /** @var string */
    private $number;

    /** @var string */
    private $type;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): self
    {
        $this->number = $number;
        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }
}
