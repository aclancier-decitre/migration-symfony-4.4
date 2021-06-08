<?php

namespace App\Repository\B2bRepository;

use App\Entity\B2bEntity\ChorusDocument;
use App\Entity\B2bEntity\ChorusDocumentFactory;
use Exception;
use GuzzleHttp\ClientInterface;

class ChorusWebService
{
    /**
     * @var ClientInterface
     */
    protected $resadecClient;

    /**
     * @var ChorusDocument
     */
    protected $chorusDocument;

    /**
     * @var ChorusDocumentFactory
     */
    protected $chorusDocumentFactory;

    /**
     * @param ClientInterface $resadecClient
     * @param ChorusDocument $chorusDocument
     * @param ChorusDocumentFactory $chorusDocumentFactory
     */
    public function __construct(
        ClientInterface $resadecClient,
        ChorusDocument $chorusDocument,
        ChorusDocumentFactory $chorusDocumentFactory
    ) {
        $this->resadecClient = $resadecClient;
        $this->chorusDocument = $chorusDocument;
        $this->chorusDocumentFactory = $chorusDocumentFactory;
    }

    /**
     * @param string $chorusDocumentId
     * @return ChorusDocument
     * @throws \Exception
     */
    public function getChorusDocumentById(string $chorusDocumentId)
    {
        try {
            return $this->chorusDocumentFactory->createFromArray(
                $this->resadecClient->get("chorus/bill-credit/" . $chorusDocumentId)->json()
            );
        } catch (Exception $e) {
            throw new \Exception("Une erreur s'est produite lors de la récupération du document", null, $e);
        }
    }

    /**
     * @param String $chorusDocumentId
     * @param ChorusDocument $chorusDocument
     * @throws \Exception
     */
    public function updateChorusDocumentById(String $chorusDocumentId, ChorusDocument $chorusDocument)
    {
        try {
            $this->resadecClient->put(
                "chorus/bill-credit/" . $chorusDocumentId,
                [
                    'body' => [
                        'typedoc' => $chorusDocument->getTypeDoc(),
                        'numsiret' => $chorusDocument->getSiretId(),
                        'numengagement' => $chorusDocument->getEngagementId(),
                        'cdservice' => $chorusDocument->getCodeService(),
                    ]
                ]
            );
        } catch (\Exception $e) {
            throw new \Exception("Une erreur s'est produite lors de la mise à jour du document", null, $e);
        }
    }
}
