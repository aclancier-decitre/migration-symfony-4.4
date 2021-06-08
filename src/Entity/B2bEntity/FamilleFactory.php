<?php

namespace App\Entity\B2bEntity;

use App\Entity\CoreEntity\Mapping;
use App\Repository\CoreRepository\MappingWebService;

class FamilleFactory
{
    /**
     * @var MappingWebService
     */
    private $mappingWebService;

    public function __construct(MappingWebService $mappingWebService)
    {
        $this->mappingWebService = $mappingWebService;
    }

    /**
     * @param array $codesFamilles
     * @param Mapping[] $mappingFamilles
     * @return Famille[]
     */
    public function createFromCodesFamillesArray(array $codesFamilles, array $mappingFamilles): array
    {
        $familles = [];

        foreach ($codesFamilles as $codeFamille) {
            /**
             * Recherche le bon code famille/libellé.
             * Le mapping ne retourne pas un tableau associatif avec les codes
             */
            foreach ($mappingFamilles as $mappingFamille) {
                if ($mappingFamille->getCode() == $codeFamille) {
                    $familles[] = new Famille($codeFamille, $mappingFamille->getLabel());
                }
            }
        }

        return $familles;
    }

    public function createFromArray(array $mappingFamilles): array
    {
        $familles = [];

        foreach ($mappingFamilles as $mappingFamille) {
            $familles[$mappingFamille->getCode()] = new Famille(
                $mappingFamille->getCode(),
                $mappingFamille->getLabel()
            );
        }

        return $familles;
    }
}
