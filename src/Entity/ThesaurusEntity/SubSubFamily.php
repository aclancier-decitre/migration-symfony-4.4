<?php

namespace App\Entity\ThesaurusEntity;

class SubSubFamily extends Family
{
    /**
     * @var SubFamily
     */
    private $parentFamily;

    public function getParentFamily(): SubFamily
    {
        return $this->parentFamily;
    }

    public function setParentFamily(SubFamily $parentFamily): self
    {
        $this->parentFamily = $parentFamily;
        return $this;
    }
}
