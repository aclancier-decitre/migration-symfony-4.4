<?php

namespace App\Repository\ThesaurusRepository;

use App\Entity\ThesaurusEntity\Family;
use App\Entity\ThesaurusEntity\SubFamily;
use App\Entity\ThesaurusEntity\SubSubFamily;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class FamilyRepository
{

    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return Family[]
     */
    public function getAllFamillyCodes(): array
    {
        $qb = $this->createQueryBuilderBase();
        $qb->addSelect('TRIM(f.cdugsoc) as management_unit_id');

        $families = [];

        if ($statement = $qb->execute()) {
            while ($family = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $families[] = $this->createFromArray($family);
            }
        }

        return $families;
    }

    /**
     * @return Family[]
     */
    public function findAllFamilyBySiteId(string $siteId): array
    {
        if (!$siteId) {
            return $this->getAllFamillyCodes();
        }

        $qb = $this->createQueryBuilderBase();
        $qb->addSelect('TRIM(u.cdug) AS management_unit_id')
            ->innerJoin('f', 'ug_fam', 'u', 'u.cdfam = f.cdfam')
            ->andWhere('CURRENT_DATE BETWEEN u.dtdebvali1 AND u.dtfinval')
            ->andWhere('u.cdsite = :siteId');
        $qb->setParameter("siteId", $siteId);
        $families = [];

        if ($statement = $qb->execute()) {
            while ($family = $statement->fetch(\PDO::FETCH_ASSOC)) {
                $families[] = $this->createFromArray($family);
            }
        }

        return $families;
    }

    private function createFromArray(array $familyInfo): Family
    {
        $family = new Family();
        $family->setId($familyInfo["id"])
            ->setLabel($familyInfo["label"])
            ->setCategory($familyInfo["category"])
            ->setManagementUnitId($familyInfo["management_unit_id"]);
        return $family;
    }

    private function createQueryBuilderBase(): QueryBuilder
    {
        $queryBuilderBase = $this->connection->createQueryBuilder();
        return $queryBuilderBase->select(
            "f.cdfam as id",
            "f.libfam as label",
            "CASE WHEN COALESCE(f.cdcategfam,'') NOT IN ('L','P') THEN '' ELSE f.cdcategfam END AS category "
        )
            ->from('famille', 'f')
            ->where('f.dtann IS NULL');
    }


    /**
     * @return Family[]
     */
    public function findAll(): array
    {
        $sql = "SELECT cdfam AS id, libfam AS label
                FROM famille
                WHERE dtann IS NULL
                ORDER BY cdfam";
        $stmt = $this->connection->executeQuery($sql);

        $families = [];
        while ($data = $stmt->fetch()) {
            $families[] = Family::create($data['id'], $data['label']);
        }
        return $families;
    }

    /**
     * @return SubFamily[]
     */
    public function findSubFamiliesByFamilyId(string $familyId): array
    {
        $sql = "SELECT
                    sf.cdsfam AS id,
                    sf.libsfam AS label,
                    f.cdfam AS parent_id,
                    f.libfam AS parent_label
                FROM sous_famille sf
                JOIN famille f ON sf.cdfam = f.cdfam
                WHERE sf.dtann IS NULL
                AND sf.cdfam = :familyId
                ORDER BY sf.cdsfam";
        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'familyId' => $familyId
        ]);

        $subFamilies = [];
        while ($data = $stmt->fetch()) {
            $parentFamily = Family::create($data['parent_id'], $data['parent_label']);

            $subFamily = new SubFamily();

            $subFamily->setId($data['id'])
                ->setLabel($data['label'])
                ->setParentFamily($parentFamily);

            $subFamilies[] = $subFamily;
        }
        return $subFamilies;
    }

    /**
     * @return SubSubFamily[]
     */
    public function findSubSubFamiliesBySubFamilyId(string $subFamilyId): array
    {
        $sql = "SELECT
                   ssf.cdssfam AS id,
                   ssf.libssfam AS label,
                   sf.cdsfam AS sub_family_id,
                   sf.libsfam AS sub_family_label,
                   f.cdfam AS family_id,
                   f.libfam AS family_label
                FROM sous_sous_famille ssf
                JOIN sous_famille sf ON ssf.cdsfam = sf.cdsfam
                JOIN famille f ON f.cdfam = sf.cdfam
                WHERE ssf.dtann IS NULL
                AND ssf.cdsfam = :subFamilyId
                ORDER BY ssf.cdssfam";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'subFamilyId' => $subFamilyId
        ]);

        $subSubFamilies = [];
        while ($data = $stmt->fetch()) {
            $family = Family::create($data['family_id'], $data['family_label']);

            $subFamily = new SubFamily();
            $subFamily->setId($data['id'])
                ->setLabel($data['label'])
                ->setParentFamily($family);

            $subSubFamily = new SubSubFamily();
            $subSubFamily->setId($data['id'])
                ->setLabel($data['label'])
                ->setParentFamily($subFamily);

            $subSubFamilies[] = $subSubFamily;
        }
        return $subSubFamilies;
    }
}
