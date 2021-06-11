<?php

namespace App\Entity\ClientEntity;

class ClientOrigin
{

    private string $id;

    private string $label;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return ClientOrigin
     */
    public function setId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return ClientOrigin
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
        return $this;
    }
}
