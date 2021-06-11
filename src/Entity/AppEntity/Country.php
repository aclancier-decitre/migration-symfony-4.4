<?php

namespace App\Entity\AppEntity;

class Country
{
    private string $code;

    private string $label;

    public const CODE_FRANCE = 'FR';

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
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

    public static function createFromArray(array $data): self
    {
        $country = new Country();
        $country->setCode($data['id'])
            ->setLabel($data['label']);

        return $country;
    }
}
