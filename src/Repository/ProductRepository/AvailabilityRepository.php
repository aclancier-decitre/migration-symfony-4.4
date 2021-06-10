<?php

namespace App\Repository\ProductRepository;

use App\Entity\ProductEntity\Availability;
use Doctrine\DBAL\Connection;

class AvailabilityRepository
{

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Availability[]
     */
    public function findAll(): array
    {
        $sql = "SELECT cddispo AS code, 
                       libdispo AS label, 
                       libdispomrdec AS short_label, 
                       cdinterditcde AS is_ordering_forbidden
                FROM dispo_vdl";
        $result = $this->connection->executeQuery($sql);

        $availabilities = [];
        while ($data = $result->fetch()) {
            $availabilities[$data['code']] = Availability::create(
                $data['code'],
                $data['label'],
                $data['short_label'],
                $data['is_ordering_forbidden']
            );
        }
        return $availabilities;
    }
}
