<?php

namespace App\Entity\B2bEntity;

class ChorusDocument
{

    /**
     * @var string
     */
    private $typeDoc;

    /**
     * @var int
     */
    private $chorusDocumentId;

    /**
     * @var string
     */
    private $siretId;

    /**
     * @var string
     */
    private $engagementId;

    /**
     * @var string
     */
    private $codeService;

    /**
     * @return string|null
     */
    public function getTypeDoc()
    {
        return $this->typeDoc;
    }

    /**
     * @param string|null $typeDoc
     * @return $this
     */
    public function setTypeDoc($typeDoc)
    {
        $this->typeDoc = $typeDoc;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getChorusDocumentId()
    {
        return $this->chorusDocumentId;
    }

    /**
     * @param int|null $chorusDocumentId
     * @return $this
     */
    public function setChorusDocumentId($chorusDocumentId)
    {
        $this->chorusDocumentId = $chorusDocumentId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSiretId()
    {
        return $this->siretId;
    }

    /**
     * @param string|null $siretId
     * @return $this
     */
    public function setSiretId($siretId)
    {
        $this->siretId = $siretId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEngagementId()
    {
        return $this->engagementId;
    }

    /**
     * @param string|null $engagementId
     * @return $this
     */
    public function setEngagementId($engagementId)
    {
        $this->engagementId = $engagementId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCodeService()
    {
        return $this->codeService;
    }

    /**
     * @param string|null $codeService
     * @return $this
     */
    public function setCodeService($codeService)
    {
        $this->codeService = $codeService;
        return $this;
    }
}
