<?php

namespace App\Entity\B2bEntity;

class PeriodeFactory
{
    /**
     * @param array $periodesData
     * @return Periode[]
     */
    public function createFromArray(array $periodesData): array
    {
        $periodes = [];

        foreach ($periodesData as $periodeData) {
            $periode = new Periode();

            // Deux possibilités de format de date (Y-m-d\TH:i:s venant de l'api et Y-m-d des inputs HTML)
            $dateDebut = \DateTime::createFromFormat('Y-m-d\TH:i:s', $periodeData['debut_de_validite']);
            $dateFin = \DateTime::createFromFormat('Y-m-d\TH:i:s', $periodeData['fin_de_validite']);

            if ($dateDebut === false || $dateFin === false) {
                $dateDebut = \DateTime::createFromFormat('Y-m-d', $periodeData['debut_de_validite']);
                $dateFin = \DateTime::createFromFormat('Y-m-d', $periodeData['fin_de_validite']);
            }

            $dateDebut->setTime(0, 0, 0, 0);
            $dateFin->setTime(0, 0, 0, 0);

            $periode->setId($periodeData['id'] ?? null)
                ->setNumeroCommande($periodeData['commande'])
                ->setDateDebut($dateDebut)
                ->setDateFin($dateFin);

            $periodes[] = $periode;
        }

        return $periodes;
    }
}
