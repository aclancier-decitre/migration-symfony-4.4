<?php

namespace App\Service\Exception;

class DatabaseActionException extends \Exception
{
    public const DEFAULT_ERROR_MESSAGE = "Erreur inconnue.";
    public const INVALID_POSTAL_CODE_OR_TOWN = "Code postal ou ville invalide.";
    public const COULD_NOT_INSERT_ADDRESS = "Erreur lors de l'insertion de l'adresse.";
    public const COULD_NOT_GENERATE_UNIQUE_CLIENT_ID = "Erreur lors de la génération de l'ID unique pour le client.";
    public const COULD_NOT_INSERT_CLIENT_EMAIL = "Erreur lors de l'insertion de l'adresse email du client.";
    public const COULD_NOT_INSERT_TELEPHONE = "Erreur lors de l'insertion du numéro de téléphone.";
    public const COULD_NOT_INSERT_MOBILE_PHONE = "Erreur lors de l'insertion du numéro de portable.";
    public const COULD_NOT_OPTIN_EMAIL = "Erreur l'ajout de l'optin email du client.";
    public const COULD_NOT_OPTIN_TELEPHONE = "Erreur l'ajout de l'optin téléphone du client.";
    public const COULD_NOT_FIND_CLIENT = "Impossible de trouver le client.";
    public const EMAIL_ALREADY_IN_USE = "L'adresse email est déjà utilisée par un client.";
    public const MOBILE_PHONE_ALREADY_IN_USE = "Le numéro de portable est déjà utilisé par un client.";
    public const MANDATORY_PARAMETER_MISSING = "Paramètre obligatoire manquant. La requête ne peut être exécutée.";

    public const COUNTRY_DOES_NOT_EXIST = "Le pays n'existe pas.";
    public const INVALID_CIVIL_CODE = "Code civil invalide.";
    public const TIMESTAMP_HAS_BEEN_MODIFIED = "Le client a été modifié par un autre utilisateur." .
    " Merci de réessayer.";
    public const COULD_NOT_GENERATE_ADDRESS_ID = "Impossible de générer un ID pour la nouvelle adresse.";
    public const INSERT_EMAIL = "Erreur lors de l'insertion de l'adresse email.";
    public const UPDATE_EMAIL = "Erreur lors de la mise à jour de l'adresse email.";
    public const NEWSLETTER_DECITRE_INFOS = "Erreur lors du traitement de l'abonnement à la newsletter email.";
    public const INSERT_PHONE = "Erreur lors de l'insertion du numéro de téléphone fixe.";
    public const UPDATE_PHONE = "Erreur lors de la mise à jour du numéro de téléphone fixe.";
    public const INSERT_MOBILE_PHONE = "Erreur lors de l'insertion du numéro de téléphone portable.";
    public const UPDATE_MOBILE_PHONE = "Erreur lors de la mise à jour du numéro de téléphone portable.";
    public const CLIENT_ALREADY_LOCKED = "Le client est déjà bloqué, opération impossible";
    public const NOT_WEB_CLIENT = "Le client n'est pas de type Internet, opération impossible.";

    public const SELECT_CLIENT = "Erreur lors de la récupération des informations client.";


    public const CLIENT_IS_VAT = "Traitement impossible : le client doit être un particulier.";

    public const CLIENT_ALREADY_HAS_CARD = "Le client possède déjà une carte de fidélité en cours de validité.";

    public const COULD_NOT_FIND_PRODUCT = "Produit inexistant.";

    private const MAPPING = [
        '40011' => self::INVALID_POSTAL_CODE_OR_TOWN,
        '40012' => self::COULD_NOT_INSERT_ADDRESS,
        '40013' => self::COULD_NOT_GENERATE_UNIQUE_CLIENT_ID,
        '40014' => self::COULD_NOT_INSERT_CLIENT_EMAIL,
        '40015' => self::COULD_NOT_INSERT_TELEPHONE,
        '40016' => self::COULD_NOT_INSERT_MOBILE_PHONE,
        '40017' => self::COULD_NOT_OPTIN_EMAIL,
        '40018' => self::EMAIL_ALREADY_IN_USE,
        '40019' => self::MOBILE_PHONE_ALREADY_IN_USE,
        '40021' => self::MANDATORY_PARAMETER_MISSING,

        '41000' => self::INVALID_CIVIL_CODE,
        '41001' => self::INVALID_POSTAL_CODE_OR_TOWN,
        '41002' => self::COUNTRY_DOES_NOT_EXIST,
        '41010' => self::TIMESTAMP_HAS_BEEN_MODIFIED,
        '41013' => self::COULD_NOT_GENERATE_ADDRESS_ID,
        '41014' => self::INSERT_EMAIL,
        '41015' => self::UPDATE_EMAIL,
        '41016' => self::NEWSLETTER_DECITRE_INFOS,
        '41017' => self::INSERT_PHONE,
        '41018' => self::UPDATE_PHONE,
        '41019' => self::INSERT_MOBILE_PHONE,
        '41020' => self::UPDATE_MOBILE_PHONE,
        '42804' => self::SELECT_CLIENT,
        '41946' => self::CLIENT_ALREADY_LOCKED,
        '41947' => self::NOT_WEB_CLIENT,

        '45000' => self::CLIENT_ALREADY_HAS_CARD,
        '43000' => self::CLIENT_IS_VAT
    ];

    public function __construct(string $sqlState = null, string $exceptionMessage = null, int $errorCode = null)
    {
        $message = '';
        if ($sqlState !== null) {
            $message = self::MAPPING[$sqlState] ?? self::DEFAULT_ERROR_MESSAGE;
        } elseif ($exceptionMessage !== null) {
            $message = $exceptionMessage;
        }

        parent::__construct($message, $errorCode ?? 500);
    }
}
