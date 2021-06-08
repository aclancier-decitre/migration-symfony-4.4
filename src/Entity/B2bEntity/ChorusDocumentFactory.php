<?php

namespace App\Entity\B2bEntity;

class ChorusDocumentFactory
{

    /**
     * @param array $chorusDocumentData
     * @return ChorusDocument
     */
    public function createFromArray(array $chorusDocumentData)
    {
        $chorusDocument = new ChorusDocument();
        $chorusDocument
            ->setTypeDoc($chorusDocumentData["typedoc"])
            ->setChorusDocumentId($chorusDocumentData["numbillcredit"])
            ->setSiretId($chorusDocumentData["numsiret"] ?? null)
            ->setEngagementId($chorusDocumentData["numengagement"] ?? null)
            ->setCodeService($chorusDocumentData["cdservice"] ?? null);

        return $chorusDocument;
    }
}
