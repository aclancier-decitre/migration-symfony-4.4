<?php

namespace App\Repository\ProductRepository;

use App\Entity\ProductEntity\ProductFormat;
use Doctrine\DBAL\Connection;

class ProductFormatRepository
{

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $sql = "SELECT cdformat AS code, libformat AS label FROM format ORDER BY libformat";
        $result = $this->connection->executeQuery($sql);

        $formats = [];
        while ($data = $result->fetch()) {
            $formats[$data['code']] = new ProductFormat($data['code'], $data['label']);
        }
        return $formats;
    }
}
