<?php

namespace App\Service\Search;

use App\Service\SolrService;
use App\Service\Tools\DatabaseDateHelper;
use App\Entity\ProductEntity\Availability;
use App\Entity\ProductEntity\ForeignLanguageEdition;
use App\Entity\ProductEntity\ProductFormat;
use App\Entity\ProductEntity\ProductSearchResult;
use App\Entity\ProductEntity\ProductType;
use App\Service\Exception\ProductSearchException;
use App\Repository\ProductRepository\AvailabilityRepository;
use App\Repository\ProductRepository\ForeignLanguageEditionRepository;
use App\Repository\ProductRepository\ProductFormatRepository;
use App\Repository\ProductRepository\ProductRepository;
use App\Repository\ProductRepository\ProductTypeRepository;
use Psr\Log\LoggerInterface;
use Solarium\QueryType\Select\Query\FilterQuery;
use Solarium\QueryType\Select\Query\Query;
use Solarium\QueryType\Select\Result\Result;

class ProductSearchService
{

    private SolrService $solrService;

    /**
     * Correspondance avec les champs Solr.
     * Certains champs peuvent être vide car ils n'ont pas de correspondance direct.
     * Dans ce cas, ils ont un traitement spécifique
     */
    private const FIELDS_MAPPING = [
        'product_title' => 'titre',
        'product_title_sort' => 'titre_tri',
        'ean' => 'ean13',
        'author_id' => 'num_auteur',
        'author_name' => 'nom_auteur',
        'publisher_id' => 'code_editeur',
        'publisher_name' => 'nom_editeur',
        'collection_id' => 'num_collection',
        'collection_name' => 'nom_collection',
        'product_format' => 'cd_format',
        'product_type' => 'type_produit',
        'foreign_language_edition' => 'cdedlangetrang',
        'published_at' => 'date_parution',
        'published_at_start' => '',
        'published_at_end' => '',
        'price' => 'pvttc',
        'price_minimum' => '',
        'price_maximum' => '',
        'availability' => 'cddispo',
        'family_code' => 'code_famille',
        'sub_family_code' => 'code_ss_famille',
        'sub_sub_family_code' => 'code_ss_ss_famille',
        'is_french' => 'cdlivrefr',
    ];

    public const FACETS = [
        'product_type',
        'product_format',
        'availability',
        'foreign_language_edition'
    ];


    private ProductRepository $productRepository;

    private ProductTypeRepository $productTypeRepository;

    private ProductFormatRepository $productFormatRepository;

    private ForeignLanguageEditionRepository $foreignLanguageEditionRepository;

    private AvailabilityRepository $availabilityRepository;

    private array $productTypes;

    private ProductFormat $productFormats;

    private array $foreignLanguageEditions;

    private array $availabilities;

    private LoggerInterface $logger;

    public function __construct(
        SolrService $solrService,
        ProductRepository $productRepository,
        ProductTypeRepository $productTypeRepository,
        ProductFormatRepository $productFormatRepository,
        ForeignLanguageEditionRepository $foreignLanguageEditionRepository,
        AvailabilityRepository $availabilityRepository,
        LoggerInterface $logger
    ) {
        $this->solrService = $solrService;
        $this->productRepository = $productRepository;
        $this->productTypeRepository = $productTypeRepository;
        $this->productFormatRepository = $productFormatRepository;
        $this->foreignLanguageEditionRepository = $foreignLanguageEditionRepository;
        $this->availabilityRepository = $availabilityRepository;
        $this->logger = $logger;
    }

    public function search(
        array $search,
        int $page = 1,
        int $resultsPerPage = 50,
        bool $totalResultsOnly = false,
        string $orderBy = 'published_at',
        string $orderDirection = 'desc'
    ): array {
        $this->validateSearch($search);

        $this->solrService->setCore(SolrService::CORE_PRODUCTS)->createSolrClient();

        /** @var Query $query */
        $query = $this->solrService->createQuery();

        $this->getRequiredData();
        $this->setQuery($search, $query);
        $this->addSolrFilterQueries($search, $query);
        $this->addSolrFacets($query);

        // Tri par pertinence = pas de tri
        if ($orderBy !== 'relevance') {
            $query->setSorts([self::FIELDS_MAPPING[$orderBy] => $orderDirection, 'titre' => 'asc']);
        }

        // On ne renvoit aucune donnée si on veut juste le nombre de résultats
        if ($totalResultsOnly) {
            $query->setStart(0)
                ->setRows(0);
        } else {
            $query->setStart(($page - 1) * $resultsPerPage)
                ->setRows($resultsPerPage);
        }

        $resultSet = $this->solrService->getSolrClient()->select($query);

        $results = [
            'totalResults' => $resultSet->getNumFound(),
        ];

        if (!$totalResultsOnly) {
            $results['productSearchResults'] = $this->processResults($resultSet);
            $results['facets'] = $this->processFacets($resultSet);
        }

        return $results;
    }

    /**
     * Défini la query q à partir du titre du produit. Le fuzzy (caractère ~) autorise les fautes de frappe
     * Si l'utilisateur recherche le titre : "piegse sur la nekge du mont-blanc"
     * La queryString générée est : titre:( "piegse"~  "sur"~  "la"~  "nekge"~  "du"~  "mont"~  "blanc"~ )
     * Le livre "Pièges sur la neige. Les conquérants du mont Blanc" sera bien retourné
     */
    private function setQuery(array $search, Query &$query)
    {
        $title = $search['product_title'];

        if (empty($title)) {
            $query->setQuery('*:*');
            return;
        }

        $helper = $query->getHelper();

        $cleanTitle = str_replace(
            ['+', '-', '&', '|', '!', '(', ')', '{', '}', '[', ']', '^', '"', '~', '*', '?', ':', '\\', '/'],
            ' ',
            $title
        );

        $titleWithFuzzy = '';
        foreach (preg_split('/(\s)+/m', $cleanTitle) as $term) {
            $titleWithFuzzy .= sprintf(' "%s"~ ', $helper->escapeTerm($term));
        }

        $queryString = sprintf(
            '%s:(%s)',
            self::FIELDS_MAPPING['product_title'],
            $titleWithFuzzy
        );

        $query->setQuery($queryString);
    }

    private function addSolrFilterQueries(array $search, Query &$query)
    {
        $helper = $query->getHelper();
        /**
         * Champs sur lesquels on peut appliquer le même type de recherche
         * Editeur et collection : si l'ID n'est pas null c'est que l'utilisateur a sélectionné une suggestion
         * dans l'autocomplétion. Dans ce cas, on ne cherche pas par nom
         */
        $oneToOneFields = [
            'ean',
            'author_name',
            $search['publisher_id'] ? 'publisher_id' : 'publisher_name',
            $search['collection_id'] ? 'collection_id' : 'collection_name',
            'family_code',
            'sub_family_code',
            'sub_sub_family_code',
            'is_french'
        ];

        // Rajoute les facets, qui seront filtrés de la même manière que des champs du formulaire
        $oneToOneFields = array_merge($oneToOneFields, self::FACETS);

        foreach ($oneToOneFields as $oneToOneField) {
            $filterQuery = null;
            /**
             * Si on a un tableau de valeurs pour un champ, on boucle sur les valeurs si il n'est pas vide
             * Si on a des objets on utilise leur méthode getCode, sinon on prend la valeur primitive
             */
            if (is_array($search[$oneToOneField])) {
                if (!empty($search[$oneToOneField])) {
                    $searchValues = [];
                    foreach ($search[$oneToOneField] as $value) {
                        $searchValues[] = is_object($value) ? $value->getCode() : $value;
                    }

                    $filterQuery = sprintf(
                        '%s:(%s)',
                        self::FIELDS_MAPPING[$oneToOneField],
                        implode(' OR ', $helper->escapeTerm($searchValues))
                    );
                }
            } else {
                // Recherche simple
                if (!empty($search[$oneToOneField])) {
                    $filterQuery = sprintf(
                        '%s:(%s)',
                        self::FIELDS_MAPPING[$oneToOneField],
                        $helper->escapeTerm($search[$oneToOneField])
                    );
                }
            }
            if (null !== $filterQuery) {
                $query->createFilterQuery(self::FIELDS_MAPPING[$oneToOneField])->setQuery($filterQuery);
            }
        }

        // Date de parution
        if (isset($search['published_at_start']) || isset($search['published_at_end'])) {
            $filterQuery = sprintf(
                '%s:[%s TO %s]',
                self::FIELDS_MAPPING['published_at'],
                $search['published_at_start'] ?
                    $search['published_at_start']->format('"Y-m-d\\T00\\:00\\:00\\Z"') : '*',
                $search['published_at_end'] ?
                    $search['published_at_end']->format('"Y-m-d\\T23\\:59\\:59\\Z"') : '*'
            );
            $query->createFilterQuery(self::FIELDS_MAPPING['published_at'])->setQuery($filterQuery);
        }

        // Prix
        if ($search['price_minimum'] || $search['price_maximum']) {
            $filterQuery = sprintf(
                '%s:[%s TO %s]',
                self::FIELDS_MAPPING['price'],
                $search['price_minimum'] ?? '*',
                $search['price_maximum'] ?? '*'
            );
            $query->createFilterQuery(self::FIELDS_MAPPING['price'])->setQuery($filterQuery);
        }

        /**
         * Exclusion des produits PL
         * Si on filtre déjà sur les types de produit on modifie la query, sinon on en créer une
         **/
        /** @var FilterQuery $currentProductTypeFilterQuery */
        $currentProductTypeFilterQuery = $query->getFilterQuery(self::FIELDS_MAPPING['product_type']);
        if (null === $currentProductTypeFilterQuery) {
            if (!empty($search['product_type'])) {
                $filterQuery = sprintf('%s:(%s)', self::FIELDS_MAPPING['product_type'], $search['product_type']);
            } else {
                $filterQuery = sprintf('NOT %s:(%s)', self::FIELDS_MAPPING['product_type'], ProductType::PLV_CODE);
            }
            $query->createFilterQuery(self::FIELDS_MAPPING['product_type'])->setQuery($filterQuery);
        } else {
            $productTypeQuery = $currentProductTypeFilterQuery->getQuery();
            $productTypeQuery .= sprintf(' NOT %s:(%s)', self::FIELDS_MAPPING['product_type'], ProductType::PLV_CODE);
            $currentProductTypeFilterQuery->setQuery($productTypeQuery);
        }
    }

    /**
     * Créer des objets ProductSearchResult à partir des documents Solr
     * @return ProductSearchResult[]
     */
    private function processResults(
        Result $result
    ): array {
        $productSearchResults = [];
        foreach ($result as $document) {
            $authorNames = $document['nom_auteur'] ?? [];

            // Récupère le type/format/edition à partir de leurs codes pour ce produit. Met à null si chaine vide ou 0.
            $productType = $this->productTypes[$document['type_produit']];
            $productFormat = !empty(trim($document['cd_format'])) ?
                $this->productFormats[$document['cd_format']] : null;
            $foreignLanguageEdition = ($document['cdedlangetrang'] ?? 0) !== 0 ?
                $this->foreignLanguageEditions[$document['cdedlangetrang']] : null;
            $collectionName = !empty(trim($document['nom_collection'])) ? $document['nom_collection'] : null;
            $availability = (null !== $document['cddispo']) ? $this->availabilities[$document['cddispo']] : null;

            $publisherName = !empty($document['nom_editeur']) ? $document['nom_editeur'] : null;
            $priceIncludingTaxes = $document['pvttc'] !== 0.0 ? (float) $document['pvttc'] : null;

            $publishedAt = DatabaseDateHelper::getDateFromString($document['date_parution']);

            $productSearchResults[] = ProductSearchResult::create(
                $document['ean13'],
                $authorNames,
                $productType,
                [
                    'title' => $document['titre'] ?? null,
                    'publisher_name' => $publisherName,
                    'collection_name' => $collectionName,
                    'published_at' => $publishedAt,
                    'format' => $productFormat,
                    'foreign_language_edition' => $foreignLanguageEdition,
                    'availability' => $availability,
                    'price_including_taxes' => $priceIncludingTaxes
                ]
            );
        }

        return $productSearchResults;
    }

    private function validateSearch(array $search)
    {
        $searchIsEmpty = true;
        foreach ($search as $index => $term) {
            if ((!is_array($term) && $term !== null) || (!empty($term))) {
                $searchIsEmpty = false;
                if (!isset(self::FIELDS_MAPPING[$index])) {
                    throw new ProductSearchException(ProductSearchException::SEARCH_FIELD_DOES_NOT_EXIST);
                }
            }
        }

        if ($searchIsEmpty) {
            throw new ProductSearchException(ProductSearchException::EMPTY_PARAMETERS_ARRAY);
        }
    }

    /**
     * Récupère tous les types, formats et éditions langue étrangères pour ne pas multiplier les appels
     */
    private function getRequiredData()
    {
        $this->productTypes = $this->productTypeRepository->findAll();
        $this->productFormats = $this->productFormatRepository->findAll();
        $this->foreignLanguageEditions = $this->foreignLanguageEditionRepository->findAll();
        $this->availabilities = $this->availabilityRepository->findAll();
    }

    /**
     * Ajout des facets qui permettent de gérer les filtres
     */
    private function addSolrFacets(Query $query)
    {
        $facetSet = $query->getFacetSet();

        foreach (self::FACETS as $facet) {
            $facetSet->createFacetField($facet)
                ->setField(self::FIELDS_MAPPING[$facet])
                ->setSort('index');
        }
    }

    /**
     * Traite le résultat des facets : retire les clés/valeurs vides ou à zéro
     * Un libellé lisible est récupéré dans le tableau correspondant au type de facet (type de produit, format...)
     */
    private function processFacets(Result $resultSet): array
    {
        $facets = $resultSet->getFacetSet()->getFacets();
        $facetsResult = [];
        foreach ($facets as $facetKey => $facetData) {
            $objectsArray = null;
            switch ($facetKey) {
                case 'product_type':
                    $objectsArray = $this->productTypes;
                    break;
                case 'product_format':
                    $objectsArray = $this->productFormats;
                    break;
                case 'availability':
                    $objectsArray = $this->availabilities;
                    break;
                case 'foreign_language_edition':
                    $objectsArray = $this->foreignLanguageEditions;
                    break;
            }

            foreach ($facetData as $key => $amount) {
                if (null === $key || $amount === 0 || empty(trim($key))) {
                    continue;
                }

                if (!isset($objectsArray[$key])) {
                    $this->logger->addWarning(
                        sprintf(
                            'Facets recherche produit : Clé inconnue "%s" pour %s. Résultat de facet ignoré.',
                            $key,
                            $facetKey
                        )
                    );
                    continue;
                }

                // Ignore PLV
                if ($facetKey === 'product_type' && $key === ProductType::PLV_CODE) {
                    continue;
                }

                $facetsResult[$facetKey][$key] = [
                    'label' => $objectsArray[$key]->getLabel(),
                    'amount' => $amount,
                ];
            }
        }
        return $facetsResult;
    }
}
