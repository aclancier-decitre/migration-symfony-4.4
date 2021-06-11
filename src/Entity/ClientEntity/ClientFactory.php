<?php

namespace App\Entity\ClientEntity;

use App\Entity\AppEntity\Address;
use App\Entity\AppEntity\Civility;
use App\Entity\AppEntity\Country;
use App\Entity\AppEntity\Email;
use App\Entity\AppEntity\Telephone;
use App\Entity\AppEntity\Timestamp;
use App\Entity\CoreEntity\Mapping;
use DateTime;
use Exception;
use Symfony\Component\Validator\Constraints\Date;

class ClientFactory
{

    /**
     * @param array $clientInfos
     * @return Client
     * @throws Exception
     */
    public function createFromArray(array $clientInfos)
    {
        $client = new Client();
        $client->setId($clientInfos['id']);
        $client->setWebId($clientInfos['web_id'] ?? null);

        $email = new Email();
        $email->setAddress($clientInfos['email']);
        $client->setEmail($email);

        $client->setNom($clientInfos['surname']);
        $client->setPrenom($clientInfos['first_name']);

        $telephone = new Telephone();
        $telephone->setNumber($clientInfos['phone'])
            ->setType('001');
        $client->setTelephone($telephone);

        $address = new Address();
        $address->setPostalCode($clientInfos['postal_code']);
        $client->setAddress($address);

        $client->setType($clientInfos['client_type'] ?? null);
        $client->setCreationDate($clientInfos['creation_date'] ?? null);
        $client->setAnonymizationDate($clientInfos['anonymization_date'] ?? null);
        $client->setCancellationDate($clientInfos['cancellation_date'] ?? null);
        $client->setCurrentOrderNumber($clientInfos['current_order_number'] ?? null);
        $client->setIsVAT($clientInfos['is_vat']);
        $client->setIsAllowedOffice($clientInfos['is_allowed_office'] ?? null);
        $client->setIsAutoOrdered($clientInfos['is_auto_ordered'] ?? null);

        if (null !== ($clientInfos['discount_card']['id'] ?? null)) {
            $carte = new Carte();
            $carte->setNumero($clientInfos['discount_card']['id']);
            $carte->setExpiration($clientInfos['discount_card']['exipration_date']);
            $carte->setTypeCode($clientInfos['discount_card']['type']['code']);
            $carte->setTypeLabel($clientInfos['discount_card']['type']['libelle']);
            $client->setCarte($carte);
        }

        if (isset($clientInfos['white_labels'])) {
            $whiteLabels = [];
            foreach ($clientInfos['white_labels'] as $whiteLabelInfo) {
                $whiteLabel = new WhiteLabel();
                $whiteLabel->setId($whiteLabelInfo['id']);
                $whiteLabel->setWebId($whiteLabelInfo['id_client_web']);
                $whiteLabel->setCurrentOrderNumber($whiteLabelInfo['current_order_number']);
                $whiteLabel->setAnonymizationDate($whiteLabelInfo['anonymization_date']);

                if (isset($whiteLabelInfo['site'])) {
                    $site = new Mapping();
                    $site->setCode($whiteLabelInfo['site']['code']);
                    $site->setLabel($whiteLabelInfo['site']['libelle']);
                    $whiteLabel->setSite($site);
                }
                $whiteLabels[] = $whiteLabel;
            }
            $client->setWhiteLabels($whiteLabels);
        }

        if (isset($clientInfos['client_origin'])) {
            $origin = new ClientOrigin();
            $origin->setId($clientInfos['client_origin']['code']);
            $origin->setLabel($clientInfos['client_origin']['libelle']);

            $client->setOrigin($origin);
        }

        return $client;
    }

    public function createFromArrayDetails(array $data): Client
    {
        $client = new Client();

        $client->setId($data['id']);

        $client->setWebId($data['webid']);

        $client->setNom($data['last_name'])
            ->setPrenom($data['first_name'])
            ->setIsAcceptingNewsletter($data['is_accepting_newsletter'])
            ->setIsAcceptingPartnersInfos($data['is_accepting_partners_infos'])
            ->setIsVAT($data['is_vat'])
            ->setType($data['type']);

        // Téléphones
        $telephone = new Telephone();
        $telephone->setId($data['telephone_id'])
            ->setNumber($data['telephone_number'])
            ->setType(Telephone::TYPE_LANDLINE_MAIN);

        $mobilePhone = new Telephone();
        $mobilePhone->setId($data['mobile_phone_id'])
            ->setNumber($data['mobile_phone_number'])
            ->setType(Telephone::TYPE_MOBILE);
        $client->setTelephone($telephone)
            ->setMobilePhone($mobilePhone);


        // Origine
        if (isset($data['origin_code'])) {
            $origin = new ClientOrigin();
            $origin->setId($data['origin_code']);
            $origin->setLabel($data['origin_label']);
            $client->setOrigin($origin);
        }

        // Email
        $email = new Email();
        $email->setId($data['email_id'])
            ->setAddress($data['email_address'])
            ->setNpai($data['email_npai'])
            ->setTypeCode($data['email_type_code']);
        $client->setEmail($email);

        // Timestamp
        $timestamp = new Timestamp();
        $timestamp
            ->setLastUpdateByOperatorDate(
                new DateTime($data['last_update_by_operator_date']) ?? null
            )
            ->setLastUpdateOperatorCode($data['last_update_operator_code'] ?? null)
            ->setLastUpdateSiteCode($data['last_update_site_code'] ?? null)
            ->setLastUpdateTimestamp($data['last_update_timestamp']);
        $client->setTimestamp($timestamp);

        // Autres dates
        $client->setCreationDate(new DateTime($data['creation_date']) ?? null)
            ->setAnonymizationDate(
                !is_null($data['anonymization_date']) ?
                    new DateTime($data['anonymization_date']) : null
            )
            ->setCancellationDate(
                !is_null($data['cancellation_date']) ?
                    new DateTime($data['cancellation_date']) : null
            )
            ->setCurrentOrderNumber($data['orders_in_progress_amount'] ?? null)
            ->setLastOrderDate(
                !is_null($data['last_order_date']) ?
                    new DateTime($data['last_order_date']) : null
            )
            ->setLockedAt(
                !is_null($data['lock_date']) ? new DateTime($data['lock_date']) : null
            );

        // Civilité
        if ($data['civility_code']) {
            $civility = Civility::createFromArray([
                'cdcivil' => $data['civility_code'],
                'libcourtcivil' => $data['civility_short_label'],
                'liblongcivil' => $data['civility_long_label'],
            ]);
            $client->setCivility($civility);
        }

        // Pays
        $country = Country::createFromArray([
            'id' => $data['country_code'],
            'label' => $data['country_label']
        ]);

        // Adresse
        $address = new Address();
        $address->setId($data['address_code'])
            ->setLine1($data['address_line1'])
            ->setLine2($data['address_line2'])
            ->setLine3($data['address_line3'])
            ->setLine4($data['address_line4'])
            ->setCity($data['city'])
            ->setCountry($country)
            ->setPostalCode($data['postal_code']);
        $client->setAddress($address);

        if ($data['card_number']) {
            $card = new Carte();
            $card->setNumero($data['card_number']);
            $card->setExpiration($data['card_expiration_date']);
            $card->setIsCdd($data['is_cdd']);
            $client->setCarte($card);
        }

        $whiteLabels = [];
        $client->setWhiteLabels($whiteLabels);

        return $client;
    }
}
