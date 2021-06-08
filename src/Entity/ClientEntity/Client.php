<?php

namespace App\Entity\ClientEntity;

use App\Entity\AppEntity\Address;
use App\Entity\AppEntity\Civility;
use App\Entity\AppEntity\Email;
use App\Entity\AppEntity\Telephone;
use App\Entity\AppEntity\Timestamp;

class Client
{
    /** @var string|null */
    const PREFIX_STAFF_CARD_NUMBER = '08';

    /**
     * @var int
     */
    public const ID_MAX_LENGTH = 8;

    /**
     * @var string
     */
    private $id;

    /** @var string|null */
    private $webId;

    /** @var Carte|null */
    private $carte;

    /** @var string */
    private $nom;

    /** @var string */
    private $prenom;

    /** @var Telephone|null */
    private $telephone;

    /** @var Telephone|null */
    private $mobilePhone;

    /** @var Email|null */
    private $email;

    /** @var ClientOrigin */
    private $origin;

    /** @var string */
    private $type;

    /** @var array */
    private $whiteLabels;

    /** @var string|\DateTime */
    private $creationDate;

    /** @var string|\DateTime */
    private $anonymizationDate;

    /** @var string|\DateTime */
    private $cancellationDate;

    /** @var integer|null */
    private $currentOrderNumber;

    /** @var bool */
    private $isVAT;

    /** @var bool|null */
    private $isAllowedOffice;

    /** @var bool|null */
    private $isAutoOrdered;

    /** @var Address */
    private $address;

    /** @var bool */
    private $isAcceptingNewsletter;

    /** @var bool */
    private $isAcceptingPartnersInfos;

    /** @var Civility */
    private $civility;

    /** @var \DateTime */
    private $lastOrderDate;

    /** @var Timestamp */
    private $timestamp;

    /** @var \DateTime */
    private $lockDate;

    public function getNumeroCarte(): ?string
    {
        if ($carte = $this->getCarte()) {
            return $carte->getNumero();
        }
        return null;
    }

    public function getExpirationCarte(): ?\DateTime
    {
        if ($carte = $this->getCarte()) {
            return $carte->getExpiration();
        }
        return null;
    }

    public function getTypeLibelleCarte(): string
    {
        if ($carte = $this->getCarte()) {
            return $carte->getTypeLibelle();
        }
        return '';
    }

    public function hasCarte(): bool
    {
        return !!$this->getCarte();
    }

    public function hasCarteExpireePourOccasion(): bool
    {
        $carte = $this->getCarte();
        return null !== $carte && $carte->isAncienneClassique() && $carte->isExpiree();
    }

    public function setCarte(Carte $carte): self
    {
        $this->carte = $carte;
        return $this;
    }

    public function getCarte(): ?Carte
    {
        return $this->carte;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;
        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?Telephone
    {
        return $this->telephone;
    }

    public function setTelephone(?Telephone $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function setEmail(Email $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function toArray(): array
    {
        return array(
            'nom' => $this->getNom(),
            'prenom' => $this->getPrenom(),
            'email' => $this->getEmail(),
            'telephone' => $this->getTelephone(),
            'carte' => $this->getCarte() ? $this->getCarte()->toArray() : array(),
            'code_postal' => $this->getAddress()->getPostalCode(),
        );
    }

    public function hasEmail(): bool
    {
        return strlen($this->getEmail()) > 0;
    }

    public function setOrigin(ClientOrigin $origin): self
    {
        $this->origin = $origin;
        return $this;
    }

    public function getOrigin(): ?ClientOrigin
    {
        return $this->origin;
    }

    public function getWebId(): ?string
    {
        return $this->webId;
    }

    public function setWebId(?string $webId): self
    {
        $this->webId = $webId;
        return $this;
    }

    /**
     * @return WhiteLabel[]
     */
    public function getWhiteLabels(): array
    {
        return $this->whiteLabels;
    }

    public function setWhiteLabels(array $whiteLabels): self
    {
        $this->whiteLabels = $whiteLabels;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function isMarketplaceClient(): bool
    {
        return ($this->type === 'PM');
    }

    public function isWhiteLabelClient(): bool
    {
        return ($this->type === 'MB');
    }

    public function haveWhiteLabel(): bool
    {
        return (count($this->whiteLabels) > 0);
    }

    public function isDecitreClient(): bool
    {
        return $this->type === 'DEC' || $this->getWebId() !== null;
    }

    /**
     * @return bool
     */
    public function isStaff(): bool
    {
        if (null === $this->carte) {
            return false;
        }

        return (self::PREFIX_STAFF_CARD_NUMBER === substr($this->carte->getNumero(), 0, 2));
    }

    /**
     * @return \DateTime|string
     */
    public function getAnonymizationDate()
    {
        return $this->anonymizationDate;
    }

    /**
     * @param \DateTime|string $anonymizationDate
     */
    public function setAnonymizationDate($anonymizationDate): self
    {
        if ($anonymizationDate instanceof \DateTime) {
            $this->anonymizationDate = $anonymizationDate;
        } elseif ($anonymizationDate !== null) {
            $this->anonymizationDate = new \DateTime($anonymizationDate);
        }
        return $this;
    }

    /**
     * @return \DateTime|string
     */
    public function getCancellationDate()
    {
        return $this->cancellationDate;
    }

    /**
     * @param \DateTime|string $cancellationDate
     */
    public function setCancellationDate($cancellationDate): self
    {
        if ($cancellationDate instanceof \DateTime) {
            $this->cancellationDate = $cancellationDate;
        } elseif ($cancellationDate !== null) {
            $this->cancellationDate = new \DateTime($cancellationDate);
        }
        return $this;
    }

    public function getCurrentOrderNumber(): ?int
    {
        return $this->currentOrderNumber;
    }

    public function setCurrentOrderNumber(?int $currentOrderNumber): self
    {
        $this->currentOrderNumber = $currentOrderNumber;
        return $this;
    }

    /**
     * @return \DateTime|string
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * @param \DateTime|string $creationDate
     */
    public function setCreationDate($creationDate): self
    {
        if ($creationDate instanceof \DateTime) {
            $this->creationDate = $creationDate;
        } elseif ($creationDate !== null) {
            $this->creationDate = new \DateTime($creationDate);
        }
        return $this;
    }

    public function haveCurrentOrder(): bool
    {
        return ($this->currentOrderNumber > 0);
    }

    public function canAnonymizeMarketplaceClient(): bool
    {
        return (
            $this->isMarketplaceClient() &&
            !$this->haveCurrentOrder() &&
            $this->anonymizationDate === null
        );
    }

    public function canAnonymizeDecitreClient(): bool
    {
        return (
            $this->isDecitreClient() &&
            !$this->haveCurrentOrder() &&
            $this->anonymizationDate === null
        );
    }

    public function canAnonymizeAtLeastOneWhiteLabels(): bool
    {
        foreach ($this->whiteLabels as $whiteLabel) {
            if ($whiteLabel->canAnonymize()) {
                return true;
            }
        }
        return false;
    }

    public function canAnonymize(): bool
    {
        return (
            $this->canAnonymizeAtLeastOneWhiteLabels() ||
            $this->canAnonymizeDecitreClient() ||
            $this->canAnonymizeMarketplaceClient()
        );
    }

    public function getIsVAT(): bool
    {
        return $this->isVAT;
    }

    public function setIsVAT(bool $isVAT): self
    {
        $this->isVAT = $isVAT;
        return $this;
    }

    public function isAllowedOffice(): ?bool
    {
        return $this->isAllowedOffice;
    }

    public function setIsAllowedOffice(?bool $isAllowedOffice): self
    {
        $this->isAllowedOffice = $isAllowedOffice;
        return $this;
    }

    public function getisAutoOrdered(): ?bool
    {
        return $this->isAutoOrdered;
    }

    public function setIsAutoOrdered(?bool $isAutoOrdered): self
    {
        $this->isAutoOrdered = $isAutoOrdered;
        return $this;
    }

    public function getAddress(): ?Address
    {
        return $this->address;
    }

    public function setAddress(?Address $address): self
    {
        $this->address = $address;
        return $this;
    }

    public function getMobilePhone(): ?Telephone
    {
        return $this->mobilePhone;
    }

    public function setMobilePhone(?Telephone $mobilePhone): self
    {
        $this->mobilePhone = $mobilePhone;
        return $this;
    }

    public function isAcceptingNewsletter(): ?bool
    {
        return $this->isAcceptingNewsletter;
    }

    public function setIsAcceptingNewsletter(?bool $isAcceptingNewsletter): self
    {
        $this->isAcceptingNewsletter = $isAcceptingNewsletter;
        return $this;
    }

    public function isAcceptingPartnersInfos(): ?bool
    {
        return $this->isAcceptingPartnersInfos;
    }

    public function setIsAcceptingPartnersInfos(?bool $isAcceptingPartnersInfos): self
    {
        $this->isAcceptingPartnersInfos = $isAcceptingPartnersInfos;
        return $this;
    }

    public function getCivility(): ?Civility
    {
        return $this->civility;
    }

    public function setCivility(?Civility $civility): self
    {
        $this->civility = $civility;
        return $this;
    }

    public function getLastOrderDate(): ?\DateTime
    {
        return $this->lastOrderDate;
    }

    public function setLastOrderDate(?\DateTime $lastOrderDate): self
    {
        $this->lastOrderDate = $lastOrderDate;
        return $this;
    }

    public function lockedAt(): ?\DateTime
    {
        return $this->lockDate;
    }

    public function setLockedAt(?\DateTime $lockDate): self
    {
        $this->lockDate = $lockDate;
        return $this;
    }

    public function getTimestamp(): Timestamp
    {
        return $this->timestamp;
    }

    public function setTimestamp(Timestamp $timestamp): self
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    public function setLastUpdateTimestamp(string $timestamp): self
    {
        $this->timestamp->setLastUpdateTimestamp($timestamp);
        return $this;
    }

    public function getLastUpdateTimestamp(): string
    {
        return $this->timestamp->getLastUpdateTimestamp();
    }

    public function isCdd(): bool
    {
        if ($carte = $this->carte) {
            return $carte->isCdd();
        }
        return false;
    }

    public function getFullName(): string
    {
        return sprintf('%s %s', $this->nom, $this->prenom);
    }
}
