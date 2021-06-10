<?php

namespace App\Repository\ProductRepository;

use App\Entity\AppEntity\Fournisseur;
use App\Entity\AppEntity\ModeTransmission;
use App\Service\Exception\DatabaseException;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\DriverException;
use Doctrine\DBAL\Query\QueryBuilder;

class FournisseurRepository
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function search(
        array $query,
        ?int $page = null,
        ?int $resultatsParPage = null,
        ?string $orderBy = null,
        ?string $orderDirection = null
    ): array {
        $qb = $this->connection->createQueryBuilder();
        $qb->select(
            'code_fournisseur AS code,
                nom_fournisseur AS nom,
                is_precre,
                gencodfour AS gencod,
                nom_editeur,
                nom_collection,
                is_principal,
                memo,
                COUNT(*) OVER() AS total_resultats'
        )
            ->from('web_recherche_fournisseur_s(:v_e_cdfour, :v_e_gencodfour, :v_e_nomfour, :v_e_nomeditr, 
            :v_e_nomcollec, :v_e_cdeditr, :v_e_cdprecre, :v_e_nomdiff)');

        $qb->setParameters([
            'v_e_cdfour' => $query['code'] ?? null,
            'v_e_gencodfour' => $query['gencod'] ?? null,
            'v_e_nomfour' => $query['nom'] ?? null,
            'v_e_nomeditr' => $query['nom_editeur'] ?? null,
            'v_e_nomcollec' => $query['nom_collection'] ?? null,
            'v_e_cdeditr' => $query['isbn_editeur'] ?? null,
            'v_e_cdprecre' => $query['is_precre'] ?? null,
            'v_e_nomdiff' => $query['nom_diffuseur'] ?? null
        ]);

        if ($page && $resultatsParPage) {
            $qb->setFirstResult(($page - 1) * $resultatsParPage)
                ->setMaxResults($resultatsParPage);
        }

        if ($orderBy && $orderDirection) {
            $qb->addOrderBy($orderBy, $orderDirection);
        }

        try {
            $stmt = $qb->execute();
        } catch (DriverException $e) {
            throw new DatabaseException($e, 'Erreur lors de la récupération des fournisseurs');
        }

        $fournisseurs = [];
        $totalResultats = 0;
        while ($data = $stmt->fetch()) {
            $totalResultats = $data['total_resultats'];
            $fournisseurs[] = $data;
        }

        return [
            'fournisseurs' => $fournisseurs,
            'totalResultats' => $totalResultats
        ];
    }

    public function findFournisseurPrincipal(string $ean): ?Fournisseur
    {
        $fournisseurPrincipal = null;
        $sql = "SELECT f.cdfour as code
                FROM fournisseur f 
               INNER JOIN pdt_four pf
                ON pf.cdfour = f.cdfour
                AND pf.cdfourprinc
                AND CURRENT_DATE BETWEEN pf.dtdebvali1 AND pf.dtfinval
                AND pf.cdpdt = :ean";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(["ean" => $ean]);
        if (!$result = $stmt->fetch()) {
            return null;
        }
        if (!empty(($searchResult = $this->search(["code" => $result["code"]]))["fournisseurs"])) {
            $fournisseur = $searchResult["fournisseurs"][0];
            $fournisseurPrincipal = new Fournisseur(
                $fournisseur["code"],
                $fournisseur["nom"],
                $fournisseur["is_precre"],
                '',
                new ModeTransmission('', '', ''),
                $fournisseur["gencod"],
                $fournisseur["memo"]
            );
        }

        /* Le mode de transmission n'est pas utile, mais n'est pas nullable et nécessite des paramètres de constructeur.
        Du coup pour ne pas casser ailleurs en modifiant le comporteent, on passe un Mode de transmission vide.
        */
        return $fournisseurPrincipal;
    }
}
