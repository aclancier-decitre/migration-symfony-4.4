<?php

namespace App\Entity\ClientEntity;

use DateTime;

class Carte
{
    public const MAPPING_CARD_TYPE_AND_ID =
        [
            'Sans Cagnottage' => '06',
            'Fidélité' => '02',
        ];

    private ?string $numero;

    private DateTime $expiration;

    private ?string $typeCode;

    private ?string $typeLabel;

    private bool $isCdd;

    /**
     * @return bool
     */
    public function isCdd(): ?bool
    {
        return $this->isCdd;
    }

    /**
     * @param bool $isCdd
     * @return Carte
     */
    public function setIsCdd(bool $isCdd): self
    {
        $this->isCdd = $isCdd;
        return $this;
    }

    public function getTypeCode(): ?string
    {
        return $this->typeCode;
    }

    public function setTypeCode(?string $typeCode): self
    {
        $this->typeCode = $typeCode;
        return $this;
    }

    public function getTypeLabel(): ?string
    {
        return $this->typeLabel;
    }

    public function setTypeLabel(?string $typeLabel): self
    {
        $this->typeLabel = $typeLabel;
        return $this;
    }

    /**
     * @param string|DateTime $expiration
     * @return self
     * @throws \Exception
     */
    public function setExpiration($expiration): self
    {
        if ($expiration instanceof DateTime) {
            $this->expiration = $expiration;
        } elseif ($expiration !== null) {
            $this->expiration = new DateTime($expiration);
        }

        return $this;
    }

    public function getExpiration(): DateTime
    {
        return $this->expiration;
    }

    public function setNumero(?string $numero)
    {
        $this->numero = $numero;

        return $this;
    }

    public function getNumero(): ?string
    {
        return $this->numero;
    }

    public function isExpiree(): bool
    {
        return $this->getExpiration() < new DateTime();
    }

    public function isOccasion(): bool
    {
        return $this->getPrefixe() === '026';
    }

    public function isAncienneClassique(): bool
    {
        return $this->getPrefixe() === '020';
    }

    public function isClassique(): bool
    {
        return $this->getPrefixe() === '029';
    }

    public function isEnseignant(): bool
    {
        return $this->getPrefixe() === '028';
    }

    public function isEtudiant(): bool
    {
        return $this->getPrefixe() === '027';
    }

    public function getTypeLibelle(): ?string
    {
        $libelles = array(
            '026' => 'Occasion',
            '027' => 'Etudiant',
            '028' => 'Enseignant',
            '029' => 'Classique',
            '020' => 'Ancienne',
        );

        $prefixe = $this->getPrefixe();

        return isset($libelles[$prefixe]) ? $libelles[$prefixe] : '';
    }

    private function getPrefixe(): string
    {
        return substr($this->getNumero(), 0, 3);
    }

    public function toArray(): array
    {
        return array(
            'numero'     => $this->getNumero(),
            'expiration' => $this->getExpiration()->format('Y-m-d'),
        );
    }

    public static function create(array $data): self
    {
        $card = new Carte();
        $card->setNumero($data['card_number'])
            ->setExpiration(new DateTime($data['card_expiration_date']))
            ->setIsCdd($data['is_cdd']);
        return $card;
    }
}
