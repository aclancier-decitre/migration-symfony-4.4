<?php

namespace App\Repository\B2bRepository;

use App\Entity\B2bEntity\CalendrierB2B;
use App\Entity\B2bEntity\CalendrierB2BFactory;
use App\Entity\B2bEntity\DeliveryB2B;
use App\Entity\B2bEntity\DeliveryB2BFactory;
use App\Entity\B2bEntity\Periode;
use App\Entity\ClientEntity\Client;
use Exception;
use GuzzleHttp\ClientInterface;
use App\Services\Factory\ProductFactory;
use GuzzleHttp\Exception\ClientException;

class OfficeWebService
{
    /**
     * @var ClientInterface
     */
    protected ClientInterface $resadecClient;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var DeliveryB2BFactory
     */
    protected $deliveryB2BFactory;

    /**
     * @var CalendrierB2BFactory
     */
    protected $calendrierB2BFactory;


    /**
     * @param ClientInterface $resadecClient
     * @param ProductFactory $productFactory
     * @param DeliveryB2BFactory $deliveryB2BFactory
     * @param CalendrierB2BFactory $calendrierB2BFactory
     */
    public function __construct(
        ClientInterface $resadecClient,
        ProductFactory $productFactory,
        DeliveryB2BFactory $deliveryB2BFactory,
        CalendrierB2BFactory $calendrierB2BFactory
    ) {
        $this->resadecClient = $resadecClient;
        $this->productFactory = $productFactory;
        $this->deliveryB2BFactory = $deliveryB2BFactory;
        $this->calendrierB2BFactory = $calendrierB2BFactory;
    }

    /**
     * @return mixed
     * @throws Exception
     */
    public function getClientsB2b()
    {
        return $this->resadecClient->get("clientsB2b")->json();
    }

    /**
     * @param $ean
     * @param $updatedQtes
     * @return mixed
     * @throws Exception
     */
    public function updateQuantities($ean, $updatedQtes)
    {
        return $this->resadecClient->post(
            "quantities/order/product/" . $ean,
            array('body' => json_encode($updatedQtes))
        )
            ->json();
    }

    /**
     * @param $deliveryId
     * @return DeliveryB2B
     * @throws Exception
     */
    public function getDeliveryById(string $deliveryId)
    {
        $response = $this->resadecClient->get("b2b/delivery/" . $deliveryId);
        return $this->deliveryB2BFactory->createFromArray($response->json());
    }

    /**
     * @param int $deliveryId
     * @param array $productsQuantities
     * @return mixed
     * @throws Exception
     */
    public function updateQuantitiesProductsInDelivery(int $deliveryId, array $productsQuantities)
    {
        $response = $this->resadecClient->put(
            "b2b/delivery/" . $deliveryId . "/updateDelivery",
            array('body' => json_encode($productsQuantities))
        );

        return $response->json();
    }

    /**
     * @param Client $client
     * @return CalendrierB2B[]
     */
    public function getCalendriersClient(Client $client): array
    {
        $response = $this->resadecClient->get('b2b/calendriers', ['query' => ['clientId' => $client->getId()]]);
        return $this->calendrierB2BFactory->createFromArray($response->json());
    }

    /**
     * @param Client $client
     * @param string $libelle
     * @return CalendrierB2B|null
     */
    public function getCalendrierClientByLibelle(Client $client, string $libelle): ?CalendrierB2B
    {
        $calendriers = $this->getCalendriersClient($client);

        $calendrier = null;
        foreach ($calendriers as $calendrierUnitaire) {
            if ($calendrierUnitaire->getLibelle() === $libelle) {
                $calendrier = $calendrierUnitaire;
            }
        }

        return $calendrier;
    }

    public function saveCalendrierClient(Client $client, CalendrierB2B $calendrier): void
    {
        $body = json_encode([
            'client_id' => $client->getId(),
            'calendriers' => [
                [
                    'libelle' => $calendrier->getLibelle(),
                    'codes_familles' => $calendrier->getCodesFamillesAssignees()
                ]
            ]
        ]);

        $this->resadecClient->post(
            "b2b/calendriers",
            array('body' => $body)
        );
    }

    public function savePeriodeOffice(Client $client, string $libelle, Periode $periode): void
    {
        $body = json_encode([
            'client_id' => $client->getId(),
            'calendriers' => [
                [
                    'libelle' => $libelle,
                    'codes_familles' => null,
                    'periodes' => [
                        [
                            'debut_de_validite' => $periode->getDateDebut()->format('c'),
                            'fin_de_validite' => $periode->getDateFin()->format('c'),
                        ]
                    ]
                ]
            ]
        ]);

        $this->resadecClient->post(
            "b2b/calendriers",
            array('body' => $body)
        );
    }

    public function updatePeriodeOffice(Client $client, Periode $periode): void
    {
        $body = json_encode([
            'client_id' => $client->getId(),
            'calendriers' => [
                [
                    'libelle' => null,
                    'codes_familles' => null,
                    'periodes' => [
                        [
                            'id' => $periode->getId(),
                            'debut_de_validite' => $periode->getDateDebut()->format('c'),
                            'fin_de_validite' => $periode->getDateFin()->format('c'),
                        ]
                    ]
                ]
            ]
        ]);

        $this->resadecClient->put(
            "b2b/calendriers",
            array('body' => $body)
        );
    }

    public function deletePeriodeOffice(int $periodeId): bool
    {
        try {
            $this->resadecClient->delete(
                sprintf(
                    'b2b/calendriers?%s',
                    http_build_query(['periodeId' => $periodeId])
                )
            );
        } catch (ClientException $e) {
            return false;
        }

        return true;
    }

    public function deleteOffice(string $clientId, string $libelle): bool
    {
        try {
            $this->resadecClient->delete(
                sprintf(
                    'b2b/calendriers?%s',
                    http_build_query(['clientId' => $clientId, 'label' => $libelle])
                )
            );
        } catch (ClientException $e) {
            return false;
        }

        return true;
    }
}
