<?php

namespace App\Service\Exception;

use Doctrine\DBAL\Driver\PDOException;
use Doctrine\DBAL\Exception\DriverException;

class DatabaseException extends \Exception
{
    /**
     * Parse une DriverException pour récupérer le message de l'exception
     */
    public function __construct(DriverException $e, ?string $defaultMessage = null)
    {
        $message = $defaultMessage ?? 'Erreur inconnue';

        $previousError = $e->getPrevious();
        if ($previousError instanceof PDOException && isset($previousError->errorInfo[2])) {
            $hasMatch = preg_match('/^ERROR:\s*(.*)\nCONTEXT/', $previousError->errorInfo[2], $matches);
            /**
             * Premier match : ligne d'erreur complète
             * Deuxième match : message d'erreur seul
             **/
            if ($hasMatch === 1 && count($matches) >= 2) {
                $message = $matches[1];
            }
        }
        parent::__construct($message, $e->getCode(), $previousError);
    }
}
