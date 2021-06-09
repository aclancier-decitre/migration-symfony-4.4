<?php

namespace App\Repository\ProductRepository;

use App\Entity\ProductEntity\Auteur;
use Doctrine\DBAL\Connection;

class AuthorRepository
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
        $sql = "SELECT DISTINCT numaut as id, nomauteur as last_name, prenomauteur as first_name
                FROM auteur WHERE nomauteur ILIKE :name || '%'
                ORDER BY prenomauteur, nomauteur";
        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'name' => $name
        ]);

        $results = [];
        while ($data = $stmt->fetch()) {
            $results[] = [
                'id' => $data['id'],
                'last_name' => $data['last_name'],
                'first_name' => $data['first_name'],
            ];
        }
        return $results;
    }

    public function findOneById(int $idAuteur): ?Auteur
    {
        $auteur = null;
        $sql = "SELECT numaut AS id, nomauteur as nom, prenomauteur as prenom
                FROM auteur
                WHERE numaut = :idAuteur";

        $statement = $this->connection->prepare($sql);
        $statement->execute([":idAuteur" => $idAuteur]);

        if ($result = $statement->fetch()) {
            $auteur = new Auteur();
            $auteur->setNumero($result["id"])
                ->setNom($result["nom"])
                ->setPrenom($result["prenom"]);
        }
        return $auteur;
    }
}
