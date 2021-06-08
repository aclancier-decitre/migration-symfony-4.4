<?php

namespace App\Entity\B2bEntity;

use App\Repository\CoreRepository\MappingWebService;

class CalendrierB2BFactory
{
    /**
     * @var MappingWebService
     */
    private $mappingWebService;

    /**
     * @var FamilleFactory
     */
    private $familleFactory;

    /**
     * @var PeriodeFactory
     */
    private $periodeFactory;

    public function __construct(
        MappingWebService $mappingWebService,
        FamilleFactory $familleFactory,
        PeriodeFactory $periodeFactory
    ) {
        $this->mappingWebService = $mappingWebService;
        $this->familleFactory = $familleFactory;
        $this->periodeFactory = $periodeFactory;
    }

    /**
     * Créer les objets CalendrierB2B depuis un array de calendriers.
     * On récupère les données des familles et périodes associées aux calendriers et on les instancie.
     * @param array $calendrierB2Bdata
     * @return CalendrierB2B[]
     */
    public function createFromArray(array $calendrierB2Bdata): array
    {
        // Mapping codes familles et libellés
        $mappingFamilles = $this->mappingWebService->getListMappingByType("famille");

        $calendriersB2B = [];

        foreach ($calendrierB2Bdata['calendriers'] as $calendrier) {
            $calendrierB2B = new CalendrierB2B();
            $calendrierB2B->setLibelle($calendrier["libelle"]);

            /**
             * Pour chaque code famille dans le calendrier, on instancie une Famille,
             * on lui affecte son code et son libellé
             */
            $calendrierB2B->setFamillesAssignees(
                $this->familleFactory->createFromCodesFamillesArray(
                    $calendrier["codes_familles"],
                    $mappingFamilles
                )
            );

            // Instanciation des périodes
            if (isset($calendrier["periodes"])) {
                $calendrierB2B->setPeriodes(
                    $this->periodeFactory->createFromArray($calendrier["periodes"])
                );
            }

            $calendriersB2B[] = $calendrierB2B;
        }

        return $calendriersB2B;
    }
}
