<?php

namespace App\Entity\ThesaurusEntity;

class Family
{
    public const LIBRARY = "L";
    public const STATIONARY = "P";

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $category;

    /**
     * @var string
     */
    private $managementUnitId;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getManagementUnitId(): string
    {
        return $this->managementUnitId;
    }

    public function setManagementUnitId(string $managementUnitId): self
    {
        $this->managementUnitId = $managementUnitId;
        return $this;
    }

    public static function create(string $id, string $label, string $category = null, string $managementUnitId = null)
    {
        $family = (new self())
            ->setId($id)
            ->setLabel($label);

        if ($category) {
            $family->setCategory($category);
        }

        if ($managementUnitId) {
            $family->setManagementUnitId($managementUnitId);
        }

        return $family;
    }
}
