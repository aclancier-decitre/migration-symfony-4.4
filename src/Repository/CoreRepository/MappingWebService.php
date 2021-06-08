<?php

namespace App\Repository\CoreRepository;

use App\Entity\CoreEntity\Mapping;
use App\Entity\CoreEntity\MappingFactory;
use GuzzleHttp\ClientInterface;

class MappingWebService
{
    /**
     * @var ClientInterface
     */
    protected $resadecClient;

    /**
     * @var MappingFactory
     */
    protected $mappingFactory;

    /**
     * @param ClientInterface $resadecClient
     * @param MappingFactory $mappingFactory
     */
    public function __construct(ClientInterface $resadecClient, MappingFactory $mappingFactory)
    {
        $this->resadecClient = $resadecClient;
        $this->mappingFactory = $mappingFactory;
    }

    /**
     * @param $typeCodeMapped
     * @return Mapping[]
     */
    public function getListMappingByType($typeCodeMapped)
    {
        $mappedCodes = array();
        $mappings = $this->resadecClient->get("mappings/".$typeCodeMapped)->json();
        foreach ($mappings as $mapping) {
            $mappedCodes[] = $this->mappingFactory->createFromArray($mapping);
        }
        return $mappedCodes ;
    }
}
