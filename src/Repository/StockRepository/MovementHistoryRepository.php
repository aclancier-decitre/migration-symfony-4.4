<?php

namespace App\Repository\StockRepository;

use App\Entity\AppEntity\Site;
use App\Entity\AppEntity\Timestamp;
use App\Service\Tools\DatabaseDateHelper;
use App\Entity\StockEntity\ClientReservationLine;
use App\Service\Exception\ProductNotFoundException;
use App\Repository\ProductRepository\ProductRepository;
use App\Entity\StockEntity\Movement;
use App\Entity\StockEntity\MovementType;
use App\Entity\StockEntity\Order;
use App\Entity\StockEntity\OrderLine;
use App\Entity\StockEntity\PhysicalFlowType;
use App\Entity\StockEntity\StockLine;
use App\Entity\StockEntity\RecapLine;
use App\Entity\StockEntity\StockType;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class MovementHistoryRepository
{

    private Connection $connection;

    private ProductRepository $productRepository;

    public function __construct(Connection $connection, ProductRepository $productRepository)
    {
        $this->connection = $connection;
        $this->productRepository = $productRepository;
    }

    /**
     * @param Site[] $sites
     * @param MovementType[]|null $movementTypes
     * @return array Nombre de résultats total et mouvements
     */
    public function findMovements(
        string $ean,
        array $sites,
        array $movementTypes = [],
        int $page = null,
        int $resultsPerPage = null,
        PhysicalFlowType $physicalFlowType = null,
        \DateTime $startDate = null,
        \DateTime $endDate = null,
        string $orderBy = null,
        string $orderDirection = null
    ): array {
        if (strlen($ean) === 0 || count($sites) === 0) {
            throw new \BadFunctionCallException('EAN et sites obligatoire.');
        }

        if (!$this->productRepository->productExists($ean)) {
            throw new ProductNotFoundException($ean);
        }

        $qb = new QueryBuilder($this->connection);

        $qb->select("*")
            ->from(
                "stock_historique_mouvements (
                    :ean,
                    string_to_array(:sites, ','),
                    string_to_array(:movementTypes, ','),
                    :physicalFlowType,
                    :startDate,
                    :endDate
               )"
            );

        if ($page && $resultsPerPage) {
            $qb->setFirstResult(($page - 1) * $resultsPerPage)
                ->setMaxResults($resultsPerPage);
        }

        if ($orderBy && $orderDirection) {
            $qb->addOrderBy($orderBy, $orderDirection);
        }

        $sitesCodes = $this->getSiteCodes($sites);
        $movementTypesCodes = $this->getMovementTypesCodes($movementTypes);

        $qb->setParameter('ean', $ean);
        $qb->setParameter('sites', implode(',', $sitesCodes));
        $qb->setParameter('movementTypes', !empty($movementTypesCodes) ? implode(',', $movementTypesCodes) : null);
        $qb->setParameter('physicalFlowType', $physicalFlowType ? $physicalFlowType->getCode() : null);
        $qb->setParameter('startDate', $startDate !== null ? $startDate->format('Ymd') : null);
        $qb->setParameter('endDate', $endDate !== null ? $endDate->format('Ymd') : null);

        $handle = $qb->execute();

        $movements = [];
        $totalFound = $totalQuantityIn = $totalQuantityOut = $totalQuantityNeutral = 0;
        while ($data = $handle->fetch()) {
            $site = new Site($data['site_code'], $data['site_name'], false, false);
            $date = new \DateTime($data['date']);
            $physicalFlowType = new PhysicalFlowType($data['flow_type_code'], $data['flow_type_code']);

            $movementType = new MovementType(
                $data['movement_type_code'],
                $physicalFlowType,
                $data['movement_type_label']
            );

            $stockTypeOrigin = $data['stock_type_origin_code'] ?
                new StockType(
                    $data['stock_type_origin_code'],
                    $data['stock_type_origin_code']
                ) : null;

            $stockTypeDestination = $data['stock_type_destination_code'] ?
                new StockType(
                    $data['stock_type_destination_code'],
                    $data['stock_type_destination_code']
                ) : null;

            $timestamp = new Timestamp($data['last_update_operator_code']);

            $movements[] = new Movement(
                $site,
                $date,
                $physicalFlowType,
                $movementType,
                $data['quantity'],
                $data['price_without_taxes'],
                $data['is_used_product'],
                $timestamp,
                $data['movement_reference'],
                $data['movement_origin'],
                $data['supplier_invoice_number'],
                $stockTypeOrigin,
                $stockTypeDestination
            );

            $totalFound = $data['total_found'];
            $totalQuantityIn = $data['total_quantity_in'];
            $totalQuantityNeutral = $data['total_quantity_neutral'];
            $totalQuantityOut = $data['total_quantity_out'];
        }

        return [
            'total' => [
                'found' => $totalFound,
                'quantity' => [
                    'in' => $totalQuantityIn,
                    'neutral' => $totalQuantityNeutral,
                    'out' => $totalQuantityOut,
                    'balance' => $totalQuantityIn - $totalQuantityOut
                ]
            ],
            'movements' => $movements
        ];
    }

    /**
     * @param Site[] $sites
     * @return string[]
     */
    private function getSiteCodes(array $sites): array
    {
        $sitesCodes = [];
        foreach ($sites as $site) {
            $sitesCodes[] = $site->getCode();
        }
        return $sitesCodes;
    }

    /**
     * @param MovementType[] $movementTypes
     * @return string[]
     */
    private function getMovementTypesCodes(array $movementTypes): array
    {
        $movementTypesCodes = [];
        foreach ($movementTypes as $movementType) {
            $movementTypesCodes[] = $movementType->getCode();
        }
        return $movementTypesCodes;
    }

    /**
     * @param string $ean
     * @param Site[] $sites
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @return StockLine[]
     */
    public function findCurrentStock(
        string $ean,
        array $sites,
        string $orderBy = null,
        string $orderDirection = null
    ): array {
        $qb = new QueryBuilder($this->connection);

        if (!$this->productRepository->productExists($ean)) {
            throw new ProductNotFoundException($ean);
        }

        $qb->select("*")
            ->from(
                "stock_quantite_en_stock (
                :ean, 
                string_to_array(:sites, ','), 
                NULL)"
            );

        if ($orderBy && $orderDirection) {
            $qb->addOrderBy($orderBy, $orderDirection);
        }

        $sitesCodes = $this->getSiteCodes($sites);

        $qb->setParameter('ean', $ean);
        $qb->setParameter('sites', implode(',', $sitesCodes));

        $handle = $qb->execute();

        $stockLines = [];
        $total = 0;
        while ($data = $handle->fetch()) {
            $site = new Site($data['site_code'], $data['site_name'], false, false);

            $stockType = new StockType($data['stock_type_code'], $data['stock_type_label']);

            $stockLines[] = new StockLine(
                $site,
                $data['is_product_used'],
                $stockType,
                $data['quantity']
            );

            $total += $data['quantity'];
        }

        return [
            'stockLines' => $stockLines,
            'total' => $total
        ];
    }

    public function findCurrentStockForEans(array $eans, string $userSiteCode): array
    {
        $qb = new QueryBuilder($this->connection);

        $qb->select('ean, site_code, site_name, stock_type_code, stock_type_label, quantity, is_product_used')
            ->from("stock_quantite_en_stock_produits_multiple(string_to_array(:eans, ','))")
            ->where("stock_type_code = 'D'");

        $qb->setParameter('eans', implode(',', $eans));

        $handle = $qb->execute();

        $stockByEan = [];
        while ($data = $handle->fetch()) {
            $site = new Site($data['site_code'], $data['site_name'], false, false);

            $stockType = new StockType($data['stock_type_code'], $data['stock_type_label']);

            if ($userSiteCode === $data['site_code']) {
                $stockByEan[$data['ean']]['current_user_site'] = new StockLine(
                    $site,
                    $data['is_product_used'],
                    $stockType,
                    $data['quantity']
                );
            } else {
                $stockByEan[$data['ean']][] = new StockLine(
                    $site,
                    $data['is_product_used'],
                    $stockType,
                    $data['quantity']
                );
            }

            // Ajout au total des quantités pour cet ean
            if (isset($stockByEan[$data['ean']]['total'])) {
                $stockByEan[$data['ean']]['total'] += $data['quantity'];
            } else {
                $stockByEan[$data['ean']]['total'] = $data['quantity'];
            }
        }

        return $stockByEan;
    }

    /**
     * Récapitulatif des mouvements de stock avec le total par mouvement par site
     * et un total par site pour tous les mouvements
     * @return RecapLine[]
     */
    public function getRecap(
        string $ean,
        array $sites,
        array $movementTypes = [],
        PhysicalFlowType $physicalFlowType = null,
        \DateTime $startDate = null,
        \DateTime $endDate = null
    ): array {
        if (strlen($ean) === 0 || count($sites) === 0) {
            throw new \InvalidArgumentException('EAN et sites obligatoire.');
        }

        if (!$this->productRepository->productExists($ean)) {
            throw new ProductNotFoundException($ean);
        }

        // Premier select : Total des quantités par site (avec prise en compte du type de flux)
        $sql = "SELECT site_code,
                       site_name,
                       site_display_order,
                       SUM(quantity) AS quantity,
                       flow_type_code,
                       movement_type_code,
                       movement_type_label
                FROM stock_historique_mouvements(
                        :ean,
                        string_to_array(:sites, ','),
                        string_to_array(:movementTypes, ','),
                        :physicalFlowType,
                        :startDate,
                        :endDate
                    )
                GROUP BY site_display_order, site_code, site_name, 
                         flow_type_code, movement_type_code, movement_type_label
                ORDER BY site_display_order, site_code, flow_type_code, quantity DESC";

        $stmt = $this->connection->prepare($sql);

        $sitesCodes = $this->getSiteCodes($sites);
        $movementTypesCodes = $this->getMovementTypesCodes($movementTypes);

        $stmt->execute([
            'ean' => $ean,
            'sites' => implode(',', $sitesCodes),
            'movementTypes' => !empty($movementTypesCodes) ? implode(',', $movementTypesCodes) : null,
            'physicalFlowType' => $physicalFlowType ? $physicalFlowType->getCode() : null,
            'startDate' => $startDate instanceof \DateTime ? $startDate->format('Ymd') : null,
            'endDate' => $endDate instanceof \DateTime ? $endDate->format('Ymd') : null
        ]);

        $recapLines = $totalsFlowType = $totalsMovementType = [];
        $uniqueSitesCodes = [];
        while ($data = $stmt->fetch()) {
            $site = new Site($data['site_code'], $data['site_name'], false, false);
            $uniqueSitesCodes[] = $data['site_code'];
            $physicalFlowType = new PhysicalFlowType($data['flow_type_code'], $data['flow_type_code']);

            $movementType = new MovementType(
                $data['movement_type_code'],
                $physicalFlowType,
                $data['movement_type_label']
            );

            $flowTypeCode = $movementType->getPhysicalFlowType()->getCode();
            $recapLines[$flowTypeCode][$data['movement_type_label']]['movement_type'] = $movementType;

            $recapLines[$flowTypeCode][$data['movement_type_label']][$data['site_code']] = [
                'quantity' => $data['quantity']
            ];

            // Total par type de flux (Entrées, Réaffectations, Sorties) par site
            if (isset($totalsFlowType[$data['site_code']][$data['flow_type_code']])) {
                $totalsFlowType[$data['site_code']][$data['flow_type_code']] += $data['quantity'];
            } else {
                $totalsFlowType[$data['site_code']][$data['flow_type_code']] = $data['quantity'];
            }

            // Total par type de flux (Entrées, Réaffectations, Sorties)
            if (isset($totalsFlowType[$data['flow_type_code']])) {
                $totalsFlowType[$data['flow_type_code']] += $data['quantity'];
            } else {
                $totalsFlowType[$data['flow_type_code']] = $data['quantity'];
            }

            // Total par type de mouvement (Sortie suite à vente en caisse, Réception à l'entrepôt...)
            if (isset($totalsMovementType[$data['movement_type_code']])) {
                $totalsMovementType[$data['movement_type_code']] += $data['quantity'];
            } else {
                $totalsMovementType[$data['movement_type_code']] = $data['quantity'];
            }
        }

        return [
            'totalsFlowType' => $totalsFlowType,
            'totalsMovementType' => $totalsMovementType,
            'uniqueSitesCodes' => array_unique($uniqueSitesCodes),
            'recapLines' => $recapLines
        ];
    }

    public function getSitesDestock(
        string $ean
    ): array {
        if (!$this->productRepository->productExists($ean)) {
            throw new ProductNotFoundException($ean);
        }

        $sql = "SELECT site_code, site_name, destock_date 
                FROM stock_sites_en_destockage (:ean, NULL)";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'ean' => $ean
        ]);

        $sitesDestock = [];
        while ($data = $stmt->fetch()) {
            $destockDate = new \DateTime($data['destock_date']);

            $sitesDestock[] = [
                'site_code' => $data['site_code'],
                'site_name' => $data['site_name'],
                'destock_date' => $destockDate
            ];
        }
        return $sitesDestock;
    }

    public function findOrders(
        string $ean,
        array $sites
    ): array {
        if (strlen($ean) === 0 || count($sites) === 0) {
            throw new \InvalidArgumentException('EAN et sites obligatoire.');
        }

        if (!$this->productRepository->productExists($ean)) {
            throw new ProductNotFoundException($ean);
        }

        $sql = "SELECT 
                    -- Commande fournisseur
                    supplier_order_date,
                    supplier_order_site_code,
                    supplier_order_site_name,
                    supplier_order_number,
    
                    supplier_order_planned_delivery_date,
                    supplier_order_label,
    
                    -- Commande besoin/client
                    order_origin_code,
                    order_origin_label,
                    order_number,
                    order_site_code,
                    order_site_name,
    
                    -- Quantités
                    ordered_quantity,
                    received_quantity,
                    underway_quantity
                FROM stock_quantite_en_commande(:ean, string_to_array(:sites, ','))";

        $stmt = $this->connection->prepare($sql);

        $sitesCodes = $this->getSiteCodes($sites);

        $stmt->execute([
            'ean' => $ean,
            'sites' => implode(',', $sitesCodes)
        ]);

        $orderLines = [];
        $totals = [
            'ordered_quantity' => 0,
            'received_quantity' => 0,
            'underway_quantity' => 0,
        ];

        while ($data = $stmt->fetch()) {
            $order = new Order(
                new Site($data['order_site_code'], $data['order_site_name'], false, false),
                $data['order_number'],
                $data['order_origin_label']
            );

            $supplierOrderDate = DatabaseDateHelper::getDateFromString($data['supplier_order_date']);
            $supplierOrder = new Order(
                new Site($data['supplier_order_site_code'], $data['supplier_order_site_name'], false, false),
                $data['supplier_order_number'],
                null,
                $supplierOrderDate
            );

            $plannedDeliveryDate = DatabaseDateHelper::getDateFromString($data['supplier_order_planned_delivery_date']);

            $orderLines[] = new OrderLine(
                $order,
                $supplierOrder,
                $data['ordered_quantity'],
                $data['received_quantity'],
                $data['underway_quantity'],
                $plannedDeliveryDate,
                $data['supplier_order_label']
            );

            $totals['ordered_quantity'] += $data['ordered_quantity'];
            $totals['received_quantity'] += $data['received_quantity'];
            $totals['underway_quantity'] += $data['underway_quantity'];
        }

        return [
            'orderLines' => $orderLines,
            'totals' => $totals
        ];
    }

    /**
     * @return ClientReservationLine[]
     */
    public function getClientReservations(
        string $ean,
        array $sites
    ): array {
        $sql = "SELECT order_site_code,
                       order_origin_code,
                       order_origin_label,
                       order_number,
                       client_id,
                       client_name,
                       quantity
               FROM stock_reservations_clients(:ean, string_to_array(:sites, ','))";

        $sitesCodes = $this->getSiteCodes($sites);

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'ean' => $ean,
            'sites' => implode(',', $sitesCodes)
        ]);

        $clientReservationLines = [];
        while ($data = $stmt->fetch()) {
            $clientReservationLines[] = new ClientReservationLine(
                $data['order_site_code'],
                $data['order_origin_label'],
                $data['order_number'],
                $data['client_id'],
                $data['client_name'],
                $data['quantity']
            );
        }

        return $clientReservationLines;
    }
}
