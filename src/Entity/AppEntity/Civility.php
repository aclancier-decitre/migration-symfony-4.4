<?php

namespace App\Entity\AppEntity;

class Civility
{
    /** @var string */
    private $id;

    /** @var string */
    private $shortLabel;

    /** @var string */
    private $longLabel;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getShortLabel(): ?string
    {
        return $this->shortLabel;
    }

    public function setShortLabel(string $shortLabel): self
    {
        $this->shortLabel = $shortLabel;
        return $this;
    }

    public function getLongLabel(): ?string
    {
        return $this->longLabel;
    }

    public function setLongLabel(string $longLabel): self
    {
        $this->longLabel = $longLabel;
        return $this;
    }

    public static function createFromArray(array $data): self
    {
        $civility = new Civility();
        $civility->setId($data['cdcivil'])
                 ->setShortLabel($data['libcourtcivil'])
                 ->setLongLabel($data['liblongcivil']);

        return $civility;
    }
}
