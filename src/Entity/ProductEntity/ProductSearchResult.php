<?php

namespace App\Entity\ProductEntity;

use App\Entity\AppEntity\Fournisseur;
use DateTime;

class ProductSearchResult
{

    private string $ean;

    private ?string $title;

    private array $authorNames = [];

    private ?string $publisherName;

    private ?string $collectionName;

    private ?Datetime $publishedAt;

    private ?ProductFormat $format;

    private ProductType $type;

    private ?ForeignLanguageEdition $foreignLanguageEdition;

    private ?Availability $availability;

    private ?float $priceIncludingTaxes;

    private ?Fournisseur $fournisseur = null;

    public function getEan(): string
    {
        return $this->ean;
    }

    public function setEan(string $ean): self
    {
        $this->ean = $ean;
        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getAuthorNames(): array
    {
        return $this->authorNames;
    }

    public function setAuthorNames(array $authorNames): self
    {
        $this->authorNames = $authorNames;
        return $this;
    }

    public function getPublisherName(): ?string
    {
        return $this->publisherName;
    }

    public function setPublisherName(?string $publisherName): self
    {
        $this->publisherName = $publisherName;
        return $this;
    }

    public function getCollectionName(): ?string
    {
        return $this->collectionName;
    }

    public function setCollectionName(?string $collectionName): self
    {
        $this->collectionName = $collectionName;
        return $this;
    }

    public function getPublishedAt(): ?\DateTime
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(?\DateTime $publishedAt): self
    {
        $this->publishedAt = $publishedAt;
        return $this;
    }

    public function getFormat(): ?ProductFormat
    {
        return $this->format;
    }

    public function setFormat(?ProductFormat $format): self
    {
        $this->format = $format;
        return $this;
    }

    public function getType(): ProductType
    {
        return $this->type;
    }

    public function setType(ProductType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getForeignLanguageEdition(): ?ForeignLanguageEdition
    {
        return $this->foreignLanguageEdition;
    }

    public function setForeignLanguageEdition(?ForeignLanguageEdition $foreignLanguageEdition): self
    {
        $this->foreignLanguageEdition = $foreignLanguageEdition;
        return $this;
    }

    public function getAvailability(): ?Availability
    {
        return $this->availability;
    }

    public function setAvailability(?Availability $availability): self
    {
        $this->availability = $availability;
        return $this;
    }

    public function getPriceIncludingTaxes(): ?float
    {
        return $this->priceIncludingTaxes;
    }

    public function setPriceIncludingTaxes(?float $priceIncludingTaxes): self
    {
        $this->priceIncludingTaxes = $priceIncludingTaxes;
        return $this;
    }

    public function getFournisseur(): ?Fournisseur
    {
        return $this->fournisseur;
    }

    public function setFournisseur(?Fournisseur $fournisseur): self
    {
        $this->fournisseur = $fournisseur;
        return $this;
    }

    public function getExtraInformations()
    {
        $extraInformations = [
            $this->publisherName,
            $this->collectionName
        ];

        if ($this->publishedAt->format('Ymd') !== '20781231') {
            $extraInformations[] = sprintf(
                '%s %s',
                ($this->publishedAt > new \DateTime()) ? 'A paraitre le' : 'Paru le',
                $this->publishedAt->format('d/m/Y')
            );
        }

        return array_filter($extraInformations, function ($item) {
            return !is_null($item);
        });
    }

    public static function create(
        string $ean,
        array $authorNames,
        ProductType $type,
        array $optionalParameters = []
    ) {
        return (new self())->setEan($ean)
            ->setAuthorNames($authorNames)
            ->setType($type)
            ->setTitle($optionalParameters['title'] ?? null)
            ->setPublisherName($optionalParameters['publisher_name'] ?? null)
            ->setCollectionName($optionalParameters['collection_name'] ?? null)
            ->setPublishedAt($optionalParameters['published_at'] ?? null)
            ->setFormat($optionalParameters['format'] ?? null)
            ->setForeignLanguageEdition($optionalParameters['foreign_language_edition'] ?? null)
            ->setAvailability($optionalParameters['availability'] ?? null)
            ->setPriceIncludingTaxes($optionalParameters['price_including_taxes'] ?? null);
    }
}
