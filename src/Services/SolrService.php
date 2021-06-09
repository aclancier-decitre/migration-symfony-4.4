<?php

namespace App\Services;

use Solarium\Core\Client\Client;
use Solarium\Core\Query\AbstractQuery;
use Solarium\Exception\InvalidArgumentException;

class SolrService
{
    public const CORE_CLIENTS = 'IndexClients';
    public const CORE_ORDERS = 'IndexCommandes';
    public const CORE_PRODUCTS = 'IndexProduits';

    protected $cores = [self::CORE_CLIENTS, self::CORE_ORDERS, self::CORE_PRODUCTS];

    /** @var array */
    private $config;

    /** @var Client */
    private $client;

    private const DEFAULT_QUERY_HANDLER = 'recherche';

    public function __construct(string $host, string $port, string $path)
    {
        $this->config = array(
            'endpoint' => array(
                'primary' => array(
                    'host' => $host,
                    'port' => $port,
                    'path' => $path,
                )
            )
        );
    }

    public function createSolrClient(): self
    {
        $this->client = new Client($this->config);
        return $this;
    }

    public function getSolrClient(): Client
    {
        return $this->client;
    }

    /**
     * @param string $core : Ensemble de documents (core/index) dans lequel on va travailler.
     * @return SolrService
     */
    public function setCore(string $core): self
    {
        if (!in_array($core, $this->cores)) {
            throw new InvalidArgumentException('Noyau invalide.');
        }

        $this->config['endpoint']['primary']['core'] = $core;
        return $this;
    }

    /**
     * Utiliser les constantes de SolrService
     * @param string|null $queryType : Type de requête sous forme de chaine. Utiliser les constantes de Client.
     * @param string|null $handler : Represente le champs Request-Handler (qt).
     * /select par défaut sur Solr, mais on utilise /recherche
     * @return AbstractQuery
     */
    public function createQuery(
        string $queryType = Client::QUERY_SELECT,
        string $handler = self::DEFAULT_QUERY_HANDLER
    ): AbstractQuery {
        if (!in_array($queryType, array_keys($this->client->getQueryTypes()))) {
            throw new InvalidArgumentException('Type de requête Solr invalide.');
        }

        return $this->client->createQuery($queryType)->setHandler($handler);
    }
}
