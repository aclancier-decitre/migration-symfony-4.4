<?php

namespace App\Repository\RepriseRepository;

use App\Entity\ClientEntity\Client;
use App\Entity\ClientEntity\ClientFactory;
use App\Service\SolrService;
use App\Entity\ClientEntity\ClientSearchResult;
use App\Entity\RepriseEntity\Livre;
use App\Entity\RepriseEntity\Reprise;
use GuzzleHttp\ClientInterface;
use Solarium\QueryType\Select\Query\Query;

class WebService
{

    protected ClientInterface $produitsHttpClient;

    protected ClientInterface $resadecClient;

    protected ClientFactory $clientFactory;

    private SolrService $solrService;

    // Mapping clé de champs formulaire => clé Solr utilisé pour la recherche
    private const FIELDS_MAPPING = [
        'last_name' => 'nomcli',
        'first_name' => 'pnomcli',
        'postal_code' => 'cdpostal',
    ];

    // Champs sur lesquels la recherche Client est effectuée
    private const FIELDS_TO_SEARCH = [
        'numcartereduc',
        'numclient',
        'nomcli',
        'pnomcli',
        'numtel',
        'cdimail',
        'ville',
        'cdpostal'
    ];

    public function __construct(
        ClientInterface $resadecClient,
        ClientFactory $clientFactory,
        ClientInterface $produitsHttpClient,
        SolrService $solrService
    ) {
        $this->resadecClient = $resadecClient;
        $this->clientFactory = $clientFactory;
        $this->produitsHttpClient = $produitsHttpClient;
        $this->solrService = $solrService;
    }

    public function searchClients(
        array $specificFields,
        string $search = null,
        int $page = 1,
        int $resultsPerPage = 10
    ): array {
        $this->solrService->setCore(SolrService::CORE_CLIENTS)->createSolrClient();
        /** @var Query $query */
        $query = $this->solrService->createQuery();

        if ($search) {
            $search = htmlspecialchars(addslashes($search));
            $query->setQuery($search);

            $edismax = $query->getEDisMax();
            $edismax->setQueryFields(implode(' ', self::FIELDS_TO_SEARCH))
                ->setMinimumMatch('100%');
        }
        $query->setStart(($page - 1) * $resultsPerPage)
            ->setRows($resultsPerPage)
            ->setQueryDefaultOperator('OR');

        // Ajout de fq pour les champs de la recherche avancée
        foreach (self::FIELDS_MAPPING as $fieldName => $solrKey) {
            if ($specificFields[$fieldName]) {
                $query->createFilterQuery($solrKey)->setQuery($solrKey . ':' . $specificFields[$fieldName]);
            }
        }

        // Client pro ou particulier
        if ($specificFields['is_vat'] === 1) {
            $query->createFilterQuery('cdtypcli')->setQuery('cdtypcli:V');
        } elseif ($specificFields['is_vat'] === 0) {
            $query->createFilterQuery('cdtypcli')->setQuery('cdtypcli:P');
        }

        $results = $this->solrService->getSolrClient()->select($query);

        $clientSearchResults = [];
        foreach ($results as $document) {
            $fullName = $document['nomcli'] . ' ' . $document['pnomcli'];

            $clientSearchResult = new ClientSearchResult(
                $document['numclient'],
                $fullName,
                $document['cdtypcli'],
                new \DateTimeImmutable($document['dtcr']),
                $document['cdtypcli'] === 'V',
                $document['topmembfam'],
                $document['cdimailaff'] ?? null,
                $document['numtelaff'] ?? null,
                $document['cdpostal'] ?? null,
                $document['numcartereduc'] ?? null
            );

            if ($clientSearchResult->isFamilyMember) {
                $clientSearchResult->familyMemberLink = $document['libtyprattachement'];
                $clientSearchResult->familyMemberFullName = $document['nommembfam'] . ' ' . $document['pnommembfam'];
            }

            $clientSearchResults[] = $clientSearchResult;
        }

        return [
            'totalResults' => $results->getNumFound(),
            'clientSearchResults' => $clientSearchResults
        ];
    }

    /**
     * @param string $id
     * @return Client|null
     */
    public function getClient(string $id)
    {
        $response = $this->resadecClient->get('clients/' . $id);

        if ($response->json() === null) {
            return null;
        }

        return $this->clientFactory->createFromArray($response->json());
    }

    /**
     * @param string $id
     * @param bool $clientDecitre
     * @param bool $sendEmail
     * @param array $whiteLabels
     * @param bool $marketplace
     * @return Client
     */
    public function anonymizeClient(
        $id,
        $clientDecitre,
        $sendEmail,
        $whiteLabels,
        $marketplace
    ) {
        $response = $this->resadecClient->put(
            'clients/' . $id . '/anonymize',
            [
                'body' => json_encode(
                    [
                        'client_decitre' => $clientDecitre,
                        'send_email' => $sendEmail,
                        'client_marketplace' => $marketplace,
                        'white_labels' => $whiteLabels,
                    ]
                )
            ]
        );
        return $this->clientFactory->createFromArray($response->json());
    }

    /**
     * @param Client $client
     *
     * @return Client
     */
    public function createClient(Client $client)
    {
        $clientInfos = $client->toArray();
        unset($clientInfos['carte']);
        $options = array(
            'body' => json_encode($clientInfos)
        );
        $response = $this->resadecClient->post('clients', $options);

        return $this->clientFactory->createFromArray($response->json());
    }

    public function toggleAllowOfficeClient($clientId, $allowOffice)
    {
        $response = $this->resadecClient->put('clients/' . $clientId . '?allow_office=' . $allowOffice);
        return $response;
    }

    public function toggleOrderAutoClient($clientId, $orderAuto)
    {
        $response = $this->resadecClient->put('clients/' . $clientId . '?order_auto=' . $orderAuto);
        return $response;
    }

    /**
     * @param Client $client
     */
    public function updateClient(Client $client, Client $clientBefore)
    {
        $clientInfos = $client->toArray();
        unset($clientInfos['code_postal']);
        unset($clientInfos['carte']);

        //La modification des adresses mail n'est pas autorisée
        if ($clientBefore->hasEmail()) {
            unset($clientInfos['email']);
        }

        $options = array(
            'body' => json_encode($clientInfos)
        );
        $this->resadecClient->put('clients/' . $client->getId(), $options);
    }

    /**
     * @param $ean
     * @return Livre|null
     */
    public function searchLivre($ean)
    {
        $httpResponse = $this->produitsHttpClient->get('products/' . $ean, ['exceptions' => false]);

        if ($httpResponse->getStatusCode() != 200) {
            return null;
        }

        $livreInfos = $httpResponse->json();

        $livre = new Livre();

        $livre
            ->setEan($livreInfos['ean'])
            ->setPoids($livreInfos['weight'])
            ->setTitre($livreInfos['name'])
            ->setCodeFamille($livreInfos['family'])
            ->setDateParution(new \DateTime($livreInfos['publication_date']));

        return $livre;
    }

    /**
     * @param Reprise $reprise
     * @return array
     * @throws \RuntimeException
     */
    public function createReprise(Reprise $reprise)
    {
        if (!$reprise->isValidable()) {
            throw new \RuntimeException("La reprise ne peut être effectuée.");
        }

        $options = array(
            'body' => json_encode($reprise->toArray())
        );
        $response = $this->resadecClient->post('reprises', $options);

        return $response->json();
    }
}
