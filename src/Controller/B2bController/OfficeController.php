<?php

namespace App\Controller\B2bController;

use App\Entity\B2bEntity\CalendrierB2B;
use App\Entity\B2bEntity\DeliveryB2B;
use App\Form\B2bForm\CalendrierFamillesType;
use App\Form\B2bForm\DeliveryB2BType;
use App\Form\B2bForm\PeriodeType;
use App\Repository\B2bRepository\OfficeWebService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RepriseRepository\WebService;


/**
 * @Route("/b2b", name="decitre_b2b_")
 */
class OfficeController extends AbstractController
{

    private WebService $webService;

    public function __construct(WebService $webService)
    {
        $this->webService = $webService;
    }

    /**
     * @Route("/", name="select_app")
     * @return Response
     */
    public function selectAppAction(): Response
    {
        return $this->render('b2b_templates/SelectApp/home.html.twig');
    }

    /**
     * @Route("/creation-commande", name="creation_commande_office")
     * @param OfficeWebService $officeWebService
     * @return Response
     * @throws Exception
     */
    public function indexAction(OfficeWebService $officeWebService): Response
    {
        $clientsArray = $officeWebService->getClientsB2b();
        return $this->render(
            'b2b_templates/CreationCommande/home.html.twig',
            array(
                'clients' => $clientsArray
            )
        );
    }

    /**
     * @Route("/calendriers/client/{clientId}", name="calendriers_show_offices", methods={"GET"}, requirements={"clientId"="\d+"})
     * @param string $clientId
     * @return Response
     */
    public function showOfficesAction(string $clientId): Response
    {
        $client = $this->webService->getClient($clientId);
        $calendriers = $this->get('office.b2b.repository')->getCalendriersClient($client);
        $mappingFamilles = $this->get('decitre.mapping.codes')->getListMappingByType("famille");
        $familles = $this->get('office.b2b.famillefactory')->createFromArray($mappingFamilles);

        $form = $this->createForm(CalendrierFamillesType::class, null, [
            'familles' => $familles,
            'calendriers' => $calendriers,
            'mode' => 'create'
        ]);

        return $this->render(
            'DecitreB2bBundle:CreationCalendrier:offices.html.twig',
            [
                'client' => $client,
                'calendriers' => $calendriers,
                'mappingFamilles' => $mappingFamilles,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Création ou édition d'un office
     * Le controller étant appelé dans une modale en Ajax, on renvoie une 400 avec un message d'erreur
     * si les données ne sont pas valides.
     * @param string $clientId
     * @param Request $request
     * @return JsonResponse
     */
    public function saveOfficeAction(string $clientId, Request $request)
    {
        $mappingFamilles = $this->get('decitre.mapping.codes')->getListMappingByType("famille");
        $allFamilles = $this->get('office.b2b.famillefactory')->createFromArray($mappingFamilles);
        $client = $this->get('client.repository')->getClient($clientId);
        if ($client === null) {
            return new JsonResponse('Client introuvable', 400);
        }
        $calendriers = $this->get('office.b2b.repository')->getCalendriersClient($client);

        $mode = $request->request->get('decitre_b2b_bundle_calendrier_familles_type')['mode'];

        $calendrier = new CalendrierB2B();
        $form = $this->createForm(CalendrierFamillesType::class, $calendrier, [
            'familles' => $allFamilles,
            'calendriers' => $calendriers,
            'mode' => $mode,
            'method' => 'POST'
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->get('office.b2b.repository')->saveCalendrierClient($client, $calendrier);

                return new JsonResponse([
                    'url' => $this->generateUrl('decitre_b2b_calendriers_show_periodes', [
                        'clientId' => $clientId,
                        'libelle' => $calendrier->getLibelle(),
                    ])
                ]);
            } catch (Exception $e) {
                $this->get('ekino.new_relic.interactor')->noticeException($e);
                return new JsonResponse(
                    ['errors' => sprintf("Erreur %d lors de la sauvegarde de l'office", $e->getCode())],
                    400
                );
            }
        }

        if ($form->getErrors(true)->count() > 0) {
            return new JsonResponse(['errors' => $form->getErrors(true)[0]->getMessage()], 400);
        }
    }

    /**
     * @param string $clientId
     * @param string $libelle
     * @return Response
     */
    public function showPeriodesAction(string $clientId, string $libelle)
    {
        $client = $this->get('client.repository')->getClient($clientId);
        if ($client === null) {
            throw new NotFoundResourceException('Client introuvable.');
        }

        $calendrier = $this->get('office.b2b.repository')->getCalendrierClientByLibelle($client, $libelle);
        if ($calendrier === null) {
            throw new NotFoundHttpException('Libelle office introuvable pour ce client');
        }

        $form = $this->createForm(PeriodeType::class, null);

        return $this->render(
            'DecitreB2bBundle:CreationCalendrier:periodes.html.twig',
            [
                'client' => $client,
                'calendrier' => $calendrier,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param string $clientId
     * @param string $libelle
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function savePeriodesAction(string $clientId, string $libelle, Request $request)
    {
        $client = $this->get('client.repository')->getClient($clientId);
        if ($client === null) {
            return new JsonResponse('Client introuvable.', 400);
        }

        $form = $this->createForm(PeriodeType::class);
        $form->handleRequest($request);

        // Création ou édition d'une période
        if ($form->isSubmitted() && $form->isValid()) {
            $periode = $form->getData();

            try {
                if ($periode->getId() === null) {
                    $this->get('office.b2b.repository')->savePeriodeOffice($client, $libelle, $periode);
                } else {
                    $this->get('office.b2b.repository')->updatePeriodeOffice($client, $periode);
                }
            } catch (Exception $e) {
                $this->get('ekino.new_relic.interactor')->noticeException($e);
                return new JsonResponse(
                    [
                        'errors' => 'Les périodes ne doivent pas se chevaucher.'
                    ],
                    400
                );
            }

            return new JsonResponse([
                'url' => $this->generateUrl('decitre_b2b_calendriers_show_periodes', [
                    'clientId' => $clientId,
                    'libelle' => $libelle,
                ])
            ]);
        }

        if ($form->getErrors()->count() > 0) {
            return new JsonResponse(['errors' => $form->getErrors()[0]->getMessage()], 400);
        }
    }

    /**
     * @param string $clientId
     * @param string $libelle
     * @param int $periodeId
     * @return RedirectResponse
     */
    public function deletePeriodeAction(string $clientId, string $libelle, int $periodeId)
    {
        $deleteOK = $this->get('office.b2b.repository')->deletePeriodeOffice($periodeId);

        if ($deleteOK) {
            $this->addFlash('success', 'Suppression effectuée avec succès.');
        } else {
            $this->addFlash('error', 'Erreur lors de la suppression.');
        }

        return $this->redirectToRoute('decitre_b2b_calendriers_show_periodes', [
            'libelle' => $libelle,
            'clientId' => $clientId,
        ]);
    }

    /**
     * @param string $clientId
     * @param string $libelle
     * @return RedirectResponse
     */
    public function deleteOfficeAction($clientId, $libelle)
    {
        $deleteOK = $this->get('office.b2b.repository')->deleteOffice($clientId, $libelle);

        if ($deleteOK) {
            $this->addFlash(
                'success',
                'Office "' . $libelle . '" supprimé avec succès'
            );
        } else {
            $this->addFlash(
                'error',
                "Erreur lors de la suppression de l'office " . $libelle
            );
        }

        return $this->redirectToRoute('decitre_b2b_calendriers_show_offices', [
            'clientId' => $clientId
        ]);
    }

    /**
     * @Route("/correction-livraison", name="correction_livraison")
     * @param Request $request
     * @return Response
     */
    public function getProduitsLivraisonACorrigerAction(Request $request): Response
    {
        $delivery = $messages = [];
        $deliveryForm = null;
        $method = $request->getMethod();
        $deliveryId = ($method == "GET") ? $request->get('deliveryId') : $request->get('delivery_form')['id'];

        try {
            if (is_numeric($deliveryId)) {
                $delivery = $this->get("office.b2b.repository")->getDeliveryById($deliveryId);

                if ($method == "POST") {
                    if ($this->updateDelivery($request, $delivery, $messages)) {
                        $delivery = [];
                    };
                }

                $deliveryForm = $this->createForm(
                    DeliveryB2BType::class,
                    $delivery,
                    ["action" => $this->generateUrl('decitre_b2b_correction_livraison')]
                )->createView();
            } elseif ($deliveryId) {
                $messages[] = [
                    'type' => 'error',
                    'message' => "Le numéro du bon de livraison n'est pas valide"
                ];

                throw new Exception();
            }
        } catch (Exception $e) {
            if (count($messages) == 0) {
                $this->get('ekino.new_relic.interactor')->noticeException($e);

                $messages[] = [
                    'type' => 'error',
                    'message' => "Une erreur s'est produite lors de l'appel aux données"
                ];
            }
        }

        return $this->render(
            'b2b_templates/CorrectionLivraison/home.html.twig',
            [
                'delivery' => $delivery,
                'deliveryId' => $deliveryId,
                'deliveryForm' => $deliveryForm,
                'messages' => $messages
            ]
        );
    }

    /**
     * @param Request $request
     * @param DeliveryB2B $deliveryB2B
     * @param array $messages
     * @return bool
     */
    private function updateDelivery(Request $request, DeliveryB2B $deliveryB2B, &$messages)
    {

        $originalDelivery = clone $deliveryB2B;
        $formData = $request->get('delivery_form');
        $formDelivery = $this->createForm(DeliveryB2BType::class, $deliveryB2B)->handleRequest($request);
        $deliveryData = $formDelivery->getData();
        $messages = [];
        $isSuccess = true;

        try {
            if ($formDelivery->isValid()) {
                $productsData = [];

                foreach ($formData['products'] as $product) {
                    $originalProduct = $originalDelivery->getProductByLineNumber($product['lineNumber']);

                    if ($product['deliveredQuantity'] > $originalProduct->getDeliveredQuantity()) {
                        $messages[] = [
                            'type' => 'error',
                            'message' => sprintf(
                                "la quantité du produit %d ne peut dépasser %d",
                                $originalProduct->getProductCode(),
                                $originalProduct->getDeliveredQuantity()
                            )
                        ];

                        throw new Exception();
                    }
                    $productsData[$product['lineNumber']] = $product['deliveredQuantity'];
                }

                $this->get("office.b2b.repository")->updateQuantitiesProductsInDelivery(
                    $deliveryData->getId(),
                    $productsData
                );

                $messages[] = [
                    'type' => 'success',
                    'message' => "Le bon de livraison a été modifié avec succès"
                ];
            } else {
                foreach ($formDelivery->getErrors() as $error) {
                    $messages[] = [
                        'type' => 'error',
                        'message' => $error
                    ];
                }
            }
        } catch (Exception $e) {
            $isSuccess = false;

            if (count($messages) == 0) {
                $this->get('ekino.new_relic.interactor')->noticeException($e);

                $messages[] = [
                    'type' => 'error',
                    'message' => "Une erreur s'est produite lors de l'enregistrement des données"
                ];
            }
        }

        return $isSuccess;
    }
}
