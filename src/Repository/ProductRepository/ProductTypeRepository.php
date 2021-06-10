<?php

namespace App\Repository\ProductRepository;

use App\Entity\ProductEntity\ProductType;
use Doctrine\DBAL\Connection;

class ProductTypeRepository
{

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findAll(): array
    {
        $sql = "SELECT cdtyppdt AS code, libtyppdt AS label FROM type_pdt ORDER BY libtyppdt";
        $result = $this->connection->executeQuery($sql);

        $types = [];
        while ($data = $result->fetch()) {
            $types[$data['code']] = new ProductType($data['code'], $data['label']);
        }
        return $types;
    }
}
