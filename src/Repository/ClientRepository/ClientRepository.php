<?php

namespace App\Repository\ClientRepository;

use App\Entity\AppEntity\Address;
use App\Entity\AppEntity\Country;
use App\Entity\AppEntity\Email;
use App\Entity\AppEntity\Telephone;
use App\Service\Exception\AddressNotFoundException;
use App\Service\Exception\DatabaseActionException;
use App\Entity\ClientEntity\Client;
use App\Entity\ClientEntity\ClientFactory;
use App\Entity\CoreEntity\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\FetchMode;

class ClientRepository
{

    private Connection $connection;

    private ClientFactory $clientFactory;

    public function __construct(Connection $connection, ClientFactory $clientFactory)
    {
        $this->connection = $connection;
        $this->clientFactory = $clientFactory;
    }

    /**
     * @param Client $client : Client à insérer en base
     * @param User $user : l'utilisateur qui a lancé la création du client
     * @return string : ID du client crée
     * @throws DatabaseActionException
     */
    public function create(Client $client, User $user): string
    {
        $sql = "SELECT r_numclient
                FROM client_particulier_i(
                  :lastName, :firstName, :civility, :addressLine1, :addressLine2, :addressLine3, :addressLine4,
                  :postalCode, :city, :country, :email, :telephone, :mobilePhone, :optinEmail,
                  :optinPartnersInfos, :siteId, :operatorId
                );
               ";
        try {
            $stmt = $this->connection->prepare($sql);

            $optinEmail = $client->isAcceptingNewsletter() === true ? '1' : '0';
            $optinPartnersInfos = $client->isAcceptingPartnersInfos() === true ? '1' : '0';

            $stmt->execute([
                'lastName' => $client->getNom(),
                'firstName' => $client->getPrenom(),
                'civility' => $client->getCivility()->getId(),
                'addressLine1' => $client->getAddress()->getLine1(),
                'addressLine2' => $client->getAddress()->getLine2(),
                'addressLine3' => $client->getAddress()->getLine3(),
                'addressLine4' => $client->getAddress()->getLine4(),
                'postalCode' => $client->getAddress()->getPostalCode(),
                'city' => $client->getAddress()->getCity(),
                'country' => $client->getAddress()->getCountry()->getCode(),
                'email' => $client->getEmail()->getAddress(),
                'telephone' => $client->getTelephone() ? $client->getTelephone()->getNumber() : null,
                'mobilePhone' => $client->getMobilePhone() ? $client->getMobilePhone()->getNumber() : null,
                'optinEmail' => $optinEmail,
                'optinPartnersInfos' => $optinPartnersInfos,
                'siteId' => $user->getSiteId(),
                'operatorId' => $user->getLogin()
            ]);
        } catch (DBALException $e) {
            throw new DatabaseActionException($e->getSqlState() ?? 0);
        }

        $clientId = $stmt->fetchColumn(0);
        return $clientId;
    }

    public function findById(string $clientId): Client
    {
        $sql = "SELECT * FROM client_particulier_details_s(:clientId);";
        try {
            $stmt = $this->connection->prepare($sql);

            $stmt->execute([
                'clientId' => $clientId
            ]);
        } catch (DBALException $e) {
            throw new DatabaseActionException($e->getSqlState() ?? 0);
        }

        $data = $stmt->fetch();

        if ($stmt->rowCount() != 1) {
            throw new DatabaseActionException(
                null,
                DatabaseActionException::COULD_NOT_FIND_CLIENT,
                404
            );
        }

        return $this->clientFactory->createFromArrayDetails($data);
    }

    /**
     * @param Client $client : Client à insérer en base
     * @param User $user : l'utilisateur qui a lancé la création du client
     * @return string : Timestamp de la modification
     * @throws DatabaseActionException
     */
    public function update(Client $client, User $user): string
    {
        $sql = "SELECT v_timestamp
                FROM client_particulier_details_u(
                  :clientId,
                  :civilCode,
                  :lastName,
                  :firstName,
                  :telephoneId,
                  :telephone,
                  :mobilePhoneId,
                  :mobilePhone,
                  :addressLine1,
                  :addressLine2, 
                  :addressLine3, 
                  :addressLine4,
                  :postalCode, 
                  :city,
                  :country, 
                  :email,
                  0,
                  :emailId,
                  :npaiEmail,
                  :optinNewsletter,
                  :optinPartnersInfos,
                  0,
                  0,
                  :lastUpdateSite,
                  :lastUpdateOperatorCode,
                  :siteCode,
                  :operatorCode,
                  :timestamp
                );
                ";

        try {
            $stmt = $this->connection->prepare($sql);

            $result = $stmt->execute([
                'clientId' => $client->getId(),
                'civilCode' => $client->getCivility()->getId(),
                'lastName' => $client->getNom(),
                'firstName' => $client->getPrenom(),
                'telephoneId' => $client->getTelephone()->getId(),
                'telephone' => $client->getTelephone()->getNumber(),
                'mobilePhoneId' => $client->getMobilePhone()->getId(),
                'mobilePhone' => $client->getMobilePhone()->getNumber(),
                'addressLine1' => $client->getAddress()->getLine1(),
                'addressLine2' => $client->getAddress()->getLine2(),
                'addressLine3' => $client->getAddress()->getLine3(),
                'addressLine4' => $client->getAddress()->getLine4(),
                'postalCode' => $client->getAddress()->getPostalCode(),
                'city' => $client->getAddress()->getCity(),
                'country' => $client->getAddress()->getCountry()->getCode(),
                'emailId' => $client->getEmail()->getId(),
                'email' => $client->getEmail()->getAddress(),
                'npaiEmail' => $client->getEmail()->isNpai() ? 1 : 0,
                'optinNewsletter' => $client->isAcceptingNewsletter() ? 1 : 0,
                'optinPartnersInfos' => $client->isAcceptingPartnersInfos() ? 1 : 0,
                'lastUpdateSite' => $user->getSiteId(),
                'lastUpdateOperatorCode' => $user->getUsername(),
                'siteCode' => $user->getSiteId(),
                'operatorCode' => $user->getUsername(),
                'timestamp' => $client->getLastUpdateTimestamp(),
            ]);
        } catch (DBALException $e) {
            throw new DatabaseActionException($e->getSqlState());
        }

        return $stmt->fetchColumn(0);
    }

    public function unlock(string $clientId): bool
    {
        try {
            $sql = "SELECT rc_cli_web_bloque_d (:clientId);";

            $stmt = $this->connection->prepare($sql);
            $result = $stmt->execute(['clientId' => $clientId]);
        } catch (DBALException $e) {
            throw new DatabaseActionException($e->getSqlState());
        }

        return $result;
    }

    public function lock(string $clientId, User $user): bool
    {
        try {
            $sql = 'SELECT rc_cli_web_bloque_i (:clientId, :hdcdoper, :hdcdsite);';

            $stmt = $this->connection->prepare($sql);
            $result = $stmt->execute([
                'clientId' => $clientId,
                'hdcdoper' => $user->getUsername(),
                'hdcdsite' => $user->getSiteId(),
            ]);
        } catch (DBALException $e) {
            throw new DatabaseActionException($e->getSqlState());
        }

        return $result;
    }

    public function getClientAddress(string $clientId): Address
    {
        $sql = "SELECT
                    adresse.numadr::VARCHAR AS id,
                    adrlgn1::VARCHAR AS line1,
                    adrlgn2::VARCHAR AS line2,
                    adrlgn3::VARCHAR AS line3,
                    adrlgn4::VARCHAR AS line4,
                    cdpostal::VARCHAR AS postal_code,
                    nomburdis::VARCHAR AS city,
                    trim(pays.cdpays) AS country_code,
                    trim(pays.libpays) AS country_label
                FROM client
                         INNER JOIN adresse ON adresse.numadr = client.numadr
                         INNER JOIN pays ON pays.cdpays = adresse.cdpays
                    AND cdtypadr = 'CO'
                WHERE client.numclient = :clientId";

        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'clientId' => $clientId,
        ]);

        $data = $stmt->fetch();

        if (!$data) {
            throw new AddressNotFoundException($clientId);
        }

        $address = (new Address())
            ->setId($data['id'])
            ->setLine1($data['line1'])
            ->setLine2($data['line2'])
            ->setLine3($data['line3'])
            ->setLine4($data['line4'])
            ->setPostalCode($data['postal_code'])
            ->setCity($data['city']);

        if ($data['country_code'] && $data['country_label']) {
            $country = (new Country())->setCode($data['country_code'])
                ->setLabel($data['country_label']);
            $address->setCountry($country);
        }
        return $address;
    }

    public function getAllEmailForClient(string $clientId): array
    {
        $sql = "SELECT DISTINCT ON (cdimail) cdimail AS address,
                             idemail AS id,
                             cdtypemail AS type,
                             COALESCE(cdnpai, 0) = 1 AS is_rts
                FROM email
                WHERE numclient = :clientId
                AND idmembfam IS NULL;";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'clientId' => $clientId
        ]);

        $emails = [];
        while ($data = $stmt->fetch()) {
            $emails[] = (new Email())
                ->setId($data['id'])
                ->setAddress($data['address'])
                ->setNpai($data['is_rts'])
                ->setTypeCode($data['type']);
        }
        return $emails;
    }

    public function getAllTelephoneNumbersForClient(string $clientId): array
    {
        $sql = "SELECT DISTINCT ON (numtel) numtel AS telephone_number, cdtypnumtel AS type, idnumtel AS id
                FROM numtelephone
                WHERE numclient = :clientId
                AND idmembfam IS NULL;";

        $stmt = $this->connection->prepare($sql);

        $stmt->execute([
            'clientId' => $clientId
        ]);

        $telephones = [];
        while ($data = $stmt->fetch()) {
            $telephones[] = (new Telephone())
                ->setId($data['id'])
                ->setType($data['type'])
                ->setNumber($data['telephone_number']);
        }
        return $telephones;
    }
}
