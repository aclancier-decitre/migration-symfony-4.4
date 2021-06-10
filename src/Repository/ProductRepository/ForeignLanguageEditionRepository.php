<?php

namespace App\Repository\ProductRepository;

use App\Entity\ProductEntity\ForeignLanguageEdition;
use Doctrine\DBAL\Connection;

class ForeignLanguageEditionRepository
{

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $sql = "SELECT cdedlangetrang AS code, 
                       libedlangetrang AS label, 
                       cdlanglibri AS libri_code, 
                       cdlanglibri2 AS second_libri_code, 
                       cdedtenfr AS is_french
                FROM edlangetrang 
                ORDER BY libedlangetrang";
        $result = $this->connection->executeQuery($sql);

        $foreignLanguageEditions = [];
        while ($data = $result->fetch()) {
            $foreignLanguageEditions[$data['code']] = new ForeignLanguageEdition(
                $data['code'],
                $data['label'],
                $data['libri_code'],
                $data['second_libri_code'],
                $data['is_french']
            );
        }
        return $foreignLanguageEditions;
    }
}
