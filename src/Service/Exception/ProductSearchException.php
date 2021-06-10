<?php

namespace App\Service\Exception;

class ProductSearchException extends \Exception
{
    public const EMPTY_PARAMETERS_ARRAY = "Vous devez spécifier au moins un critère de recherche.";
    public const SEARCH_FIELD_DOES_NOT_EXIST = "Champ de recherche inexistant.";

    public function __construct(string $exceptionMessage = null, int $errorCode = null, \Throwable $previous = null)
    {
        parent::__construct($exceptionMessage, $errorCode ?? 500, $previous);
    }
}
