<?php

namespace App\Entity\ThesaurusEntity;

class SubFamily extends Family
{
    /**
     * @var Family
     */
    private $parentFamily;

    public function getParentFamily(): Family
    {
        return $this->parentFamily;
    }

    public function setParentFamily(Family $parentFamily): void
    {
        $this->parentFamily = $parentFamily;
    }
}
