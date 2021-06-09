<?php

namespace App\Repository\ProductRepository;

use App\Entity\ProductEntity\Editeur;
use Doctrine\DBAL\Connection;

class PublisherRepository
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function searchByName(string $name): array
    {
        $sql = "SELECT DISTINCT trim(cdeditr) as id, nomeditr as name 
                FROM editeur WHERE nomeditrminus ILIKE (:name) || '%'
                ORDER BY nomeditr";
        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'name' => $name
        ]);

        $results = [];
        while ($data = $stmt->fetch()) {
            $results[] = [
                'id' => $data['id'],
                'name' => $data['name'],
            ];
        }
        return $results;
    }

    public function findOneById(string $idEditeur): ?Editeur
    {
        $editeur= null;
        $sql = "SELECT cdeditr AS id, nomeditr as nom
                FROM editeur
                WHERE cdeditr = :idEditeur";

        $statement = $this->connection->prepare($sql);
        $statement->execute([":idEditeur" => $idEditeur]);
        if ($result = $statement->fetch()) {
            $editeur = new Editeur();
            $editeur->setId($result["id"])
                ->setNom($result["nom"]);
        }
        return $editeur;
    }
}
