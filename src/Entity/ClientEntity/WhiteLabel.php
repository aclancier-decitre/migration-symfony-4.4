<?php

namespace App\Entity\ClientEntity;

use App\Entity\CoreEntity\Mapping;
use DateTime;
use Exception;

class WhiteLabel
{

    private string $id;

    private string $webId;

    /**
     * @var string|\DateTime
     */
    private $anonymizationDate;

    private Mapping $site;

    private ?int $currentOrderNumber;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return WhiteLabel
     */
    public function setId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getWebId(): string
    {
        return $this->webId;
    }

    /**
     * @param string $webId
     * @return WhiteLabel
     */
    public function setWebId(string $webId)
    {
        $this->webId = $webId;
        return $this;
    }

    /**
     * @return null|\DateTime
     */
    public function getAnonymizationDate()
    {
        return $this->anonymizationDate;
    }

    /**
     * @param string|\DateTime $anonymizationDate
     * @return WhiteLabel
     * @throws Exception
     */
    public function setAnonymizationDate($anonymizationDate)
    {
        if ($anonymizationDate instanceof DateTime) {
            $this->anonymizationDate = $anonymizationDate;
        } elseif ($anonymizationDate !== null) {
            $this->anonymizationDate = new DateTime($anonymizationDate);
        }
        return $this;
    }

    /**
     * @return null|Mapping
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param Mapping $site
     * @return WhiteLabel
     */
    public function setSite(Mapping $site)
    {
        $this->site = $site;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getCurrentOrderNumber(): int
    {
        return $this->currentOrderNumber;
    }

    /**
     * @param int|null $currentOrderNumber
     * @return WhiteLabel
     */
    public function setCurrentOrderNumber(int $currentOrderNumber)
    {
        $this->currentOrderNumber = $currentOrderNumber;
        return $this;
    }

    /**
     * @return bool
     */
    public function haveCurrentOrder() : bool
    {
        return ($this->currentOrderNumber > 0);
    }

    /**
     * @return bool
     */
    public function canAnonymize() : bool
    {
        return (
            !$this->haveCurrentOrder() &&
            $this->anonymizationDate === null
        );
    }

    public static function createFromArray(array $datum): self
    {
        $whiteLabel = new WhiteLabel();
        $whiteLabel->setId($datum['id'])
            ->setWebId($datum['id_client_web'])
            ->setCurrentOrderNumber($datum['current_order_number'])
            ->setAnonymizationDate($datum['anonymization_date']);

        return $whiteLabel;
    }
}
