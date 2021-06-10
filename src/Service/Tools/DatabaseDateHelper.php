<?php

namespace App\Service\Tools;

class DatabaseDateHelper
{
    /*
     * Les dates minimums et maximums définies ici sont liées aux contraintes de DateTime
     * dans la base de données.
     * Etant donné la possible utilisation de cette limite partout dans l'application, on défini cette classe
     * dans le CoreBundle.
     */
    const SYBASE_MIN_DATE = "19010101";
    const SYBASE_MAX_DATE = "20781231";

    /**
     * @return \DateTime
     */
    public static function getSybaseMinDate()
    {
        return new \DateTime(self::SYBASE_MIN_DATE);
    }

    /**
     * @return \DateTime
     */
    public static function getSybaseMaxDate()
    {
        return new \DateTime(self::SYBASE_MAX_DATE);
    }

    public static function getDateFromString(?string $stringDate): ?\DateTime
    {
        $date = null;

        if ($stringDate !== null) {
            try {
                $date = new \DateTime($stringDate);
            } catch (\Exception $e) {
                throw new \Exception(
                    sprintf(
                        'Format de date invalide %s',
                        $stringDate
                    )
                );
            }
        }
        return $date;
    }
}
