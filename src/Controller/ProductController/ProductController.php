<?php

namespace App\Controller\ProductController;

use App\Form\ProductForm\DecitreWebSearchType;
use App\Form\ProductForm\GetProductType;
use App\Services\Http\BinaryTempFileResponse;
use App\Repository\ProductRepository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository\OrbProductRepository;
use App\Services\Exception\DatabaseActionException;

/**
 * @Route("/product", name="decitre_")
 */
class ProductController extends AbstractController
{

    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/", name="product_homepage")
     * @param string $title
     * @param string $action
     * @return Response
     */
    public function indexAction(string $title, string $action): Response
    {
        $getProductForm = $this->createForm(GetProductType::class, null);

        return $this->render(
            'product_templates/get-product.html.twig',
            [
                'title' => $title,
                'action' => $action,
                'form' => $getProductForm->createView(),
            ]
        );
    }


    public function searchAction(Request $request)
    {
        $form = $this->createForm(DecitreWebSearchType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();
            $url = $data['url'];
            $urlParser = $this->get('b2c.url_parser');

            if (!$urlParser->isDecitreUrl($url)) {
                $this->get('session')->getFlashBag()->add('error', "L'url n'est pas une url decitre.fr");

                return $this->renderPage($form);
            }

            $parsedUrl = $urlParser->parseUrl($url);

            try {
                if ($urlParser->isSearchUrl($url)) {
                    $products = $this->get('b2c.api.search')->search($parsedUrl['query']);
                } else {
                    $products = $this->get('b2c.api.search')->category($parsedUrl['path'], $parsedUrl['query'] ?? '');
                }
            } catch (\Exception $e) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    "Impossible d'exporter les résultats. Veuillez vérifier l'url saisie"
                );

                return $this->renderPage($form);
            }

            $exportFileName = $this->get('b2c.export.products')->export($products);
            $filename = sprintf('export_produit_%s.csv', (new \DateTime())->format('Y-m-d-H-i-s'));

            return new BinaryTempFileResponse($exportFileName, Response::HTTP_OK, [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Content-disposition' =>  'attachment;filename="' . $filename . '"',
            ]);
        }

        return $this->renderPage($form);
    }

    private function renderPage(Form $form): Response
    {
        return $this->render('product_templates/search.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{ean}/current-orders-quantities", name="product_current_orders_quantities_index")
     * @param string $ean
     * @param ProductRepository $productRepository
     * @return Response
     */
    public function currentOrdersQuantitiesAction(string $ean, ProductRepository $productRepository): Response
    {
        $currentOrdersQuantities = $productRepository->getCurrentOrdersQuantities($this->getUser()->getSiteId(), $ean);

        return $this->render('product_templates/current-orders-quantities/index.html.twig', [
            'currentOrdersQuantities' => $currentOrdersQuantities
        ]);
    }

    /**
     * @Route("/{ean}", name="product_fiche")
     * @param string $ean
     * @param OrbProductRepository $orbProductRepository
     * @return Response
     */
    public function ficheAction(string $ean, OrbProductRepository $orbProductRepository): Response
    {
        $donneesOrb = null;
        try {
            $donneesOrb = $orbProductRepository->getProduct($ean);
        } catch (\Exception $e) {
            $this->get('ekino.new_relic.interactor')->noticeException($e);
            $this->addFlash('warning', 'Impossible de récupérer les données ORB');
        }

        try {
            $produit = $this->productRepository->getInfosProduit($ean, [
                'familles',
                'resume',
                'biographie',
                'auteur',
                'prix',
                'fournisseur',
                'editeur',
                'serie-revue',
                'collection',
                'edition',
                'marque-editoriale',
                'format',
                'presentation',
                'dimensions',
                'disponibilite',
            ]);
        } catch (\Exception $e) {
            $message = 'Erreur lors de la récupération des informations produit';
            if ($e instanceof DatabaseActionException) {
                $message = $e->getMessage();
            }
            $this->get('ekino.new_relic.interactor')->noticeException($e);
            $this->addFlash('error', $message);
            return $this->redirectToRoute('decitre_product_search_index');
        }

        return $this->render('@DecitreProduct/fiche/index.html.twig', [
            'produit' => $produit,
            'donneesOrb' => $donneesOrb,
        ]);
    }

}
