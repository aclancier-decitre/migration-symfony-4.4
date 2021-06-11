<?php

namespace App\Entity\ClientEntity;

class ClientSearchResult
{
    public string $clientId;
    public string $fullName;
    public string $type;
    public \DateTimeImmutable $creationDate;
    public bool $isVAT;
    public bool $isFamilyMember;

    public ?string $email = null;
    public ?string $telephone = null;
    public ?string $postalCode = null;
    public ?string $cardNumber = null;
    public ?string $familyMemberLink = null;
    public ?string $familyMemberFullName = null;

    public function __construct(
        string $clientId,
        string $fullName,
        string $type,
        \DateTimeImmutable $creationDate,
        bool $isVAT,
        bool $isFamilyMember,
        ?string $email,
        ?string $telephone,
        ?string $postalCode,
        ?string $cardNumber
    ) {
        $this->clientId = $clientId;
        $this->fullName = $fullName;
        $this->type = $type;
        $this->creationDate = $creationDate;
        $this->isVAT = $isVAT;
        $this->isFamilyMember = $isFamilyMember;
        $this->email = $email;
        $this->telephone = $telephone;
        $this->postalCode = $postalCode;
        $this->cardNumber = $cardNumber;
    }
}
