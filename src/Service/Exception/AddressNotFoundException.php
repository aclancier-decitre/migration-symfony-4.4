<?php

namespace App\Service\Exception;

class AddressNotFoundException extends \Exception
{
    public function __construct(string $clientId)
    {
        parent::__construct("Adresse non trouvée pour le client $clientId");
    }
}
