<?php

namespace App\Entity\CoreEntity;

class MappingFactory
{
    /**
     * @param array $codeInformations
     * @return Mapping
     */
    public static function createFromArray($codeInformations)
    {
        $mapping = new Mapping();
        $mapping->setCode($codeInformations["code"])
            ->setLabel($codeInformations["libelle"]);

        return $mapping;
    }
}
