<?php

namespace App\Controller\AppController\Api;

use App\Service\Exception\AddressNotFoundException;
use App\Service\Exception\DatabaseActionException;
use App\Repository\ClientRepository\ClientRepository;
// use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;


class ClientApiController extends Controller
{
    /**
     * @Route("/clients/{id}", name="decitre_api_clients", methods={"GET"})
     */
    public function getAction(string $id, ClientRepository $clientRepository)
    {
        try {
            $client = $clientRepository->findById($id);
        } catch (DatabaseActionException $e) {
            $this->get('ekino.new_relic.interactor')->noticeException($e);
            return $this->json([
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            $this->get('ekino.new_relic.interactor')->noticeException($e);
            return $this->json([
                'message' => 'Erreur lors de la récupération du client'
            ], 500);
        }

        return $this->json([
            'client' => $client->toArray(),
        ]);
    }

    public function getAddressAction(
        string $id,
        ClientRepository $clientRepository
//        SerializerInterface $serializer
    ) {
        try {
            $address = $clientRepository->getClientAddress($id);
        } catch (AddressNotFoundException $e) {
            return $this->json(['message' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            $this->get('ekino.new_relic.interactor')->noticeException($e);
            return $this->json(['message' => "Erreur lors de la récupération de l'adresse du client."], 500);
        }
//        $data = $serializer->serialize(['address' => $address], 'json');
//        return new JsonResponse($data, 200, [], true);
    }

    public function getAllEmailAction(
        string $clientId,
        ClientRepository $clientRepository
//        SerializerInterface $serializer
    ) {
        try {
            $emails = $clientRepository->getAllEmailForClient($clientId);
        } catch (\Exception $e) {
            $this->get('ekino.new_relic.interactor')->noticeException($e);
            return $this->json([
                'message' => 'Erreur lors de la récupération des adresses email du client'
            ], 500);
        }

//        $data = $serializer->serialize(['emails' => $emails], 'json');
//        return new JsonResponse($data, 200, [], true);
    }

    public function getAllTelephoneNumbersAction(
        string $clientId,
        ClientRepository $clientRepository
//        SerializerInterface $serializer
    ) {
        try {
            $telephones = $clientRepository->getAllTelephoneNumbersForClient($clientId);
        } catch (\Exception $e) {
            $this->get('ekino.new_relic.interactor')->noticeException($e);
            return $this->json([
                'message' => 'Erreur lors de la récupération des numéros de téléphone du client'
            ], 500);
        }

//        $data = $serializer->serialize(['telephones' => $telephones], 'json');
//        return new JsonResponse($data, 200, [], true);
    }
}
