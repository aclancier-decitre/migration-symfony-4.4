<?php

namespace App\Controller\B2bController;

use App\Form\B2bForm\ChorusDocumentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ChorusController extends AbstractController
{
    /**
     * @param Request $request
     * @return Response
     */
    public function chorusDocumentAction(Request $request)
    {
        $chorusForm = $chorusDocument = null;
        $messages = [];
        $chorusDocumentId = ($request->get('chorusDocumentId')) ?? $request->get('chorus_document')['chorusDocumentId'];

        try {
            $chorusDocument = ($chorusDocumentId)
                ? $this->get("chorus.repository")->getChorusDocumentById($chorusDocumentId)
                : null;

            if ($chorusDocument && $chorusDocument->getChorusDocumentId()) {
                $chorusForm = $this->createForm(
                    ChorusDocumentType::class,
                    $chorusDocument,
                    ["action" => $this->generateUrl('decitre_b2b_correction_document_chorus')]
                );

                if ($request->getMethod() == 'POST') {
                    $chorusForm = $chorusForm->handleRequest($request);

                    if ($chorusForm->isValid()) {
                        $this->get("chorus.repository")->updateChorusDocumentById(
                            $chorusDocumentId,
                            $chorusForm->getData()
                        );
                        $chorusForm = null;

                        $messages[] = [
                            'type' => 'success',
                            'message' => 'Le document a été mise à jour avec succès',
                        ];
                    }
                }
            } elseif ($chorusDocumentId != null) {
                throw new \Exception("Numéro de document incorrect ou non modifiable");
            }
        } catch (\Exception $e) {
            $messages[] = [
                'type' => 'error',
                'message' => $e->getMessage(),
            ];
        }

        return $this->render(
            'DecitreB2bBundle:Chorus:correction-document-chorus.html.twig',
            [
                'messages' => $messages,
                'chorusDocumentId' => $chorusDocumentId,
                'chorusDocument' => $chorusDocument,
                'chorusForm' => ($chorusForm) ? $chorusForm->createView() : null,
            ]
        );
    }
}
