<?php

namespace App\Service\Exception;

class ProductNotFoundException extends \Exception
{
    public function __construct(string $ean)
    {
        parent::__construct("Le produit $ean est introuvable.");
    }
}
