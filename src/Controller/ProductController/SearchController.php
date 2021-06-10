<?php

namespace App\Controller\ProductController;

use App\Service\PaginatorService;
use App\Service\Exception\ProductSearchException;
use App\Form\ProductForm\ProductSearchType;
use App\Repository\ProductRepository\FournisseurRepository;
use App\Service\Search\ProductSearchService;
use App\Repository\StockRepository\MovementHistoryRepository;
use App\Repository\ProductRepository\OrbProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Form;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/product", name="decitre_")
 */
class SearchController extends AbstractController
{

    /**
     * @Route("/search", name="product_search_index")
     * @param Request $request
     * @param ProductSearchService $productSearchService
     * @param PaginatorService $paginatorService
     * @param MovementHistoryRepository $movementHistoryRepository
     * @param OrbProductRepository $orbProductRepository
     * @param FournisseurRepository $fournisseurRepository
     * @return Response
     */
    public function indexAction(
        Request $request,
        ProductSearchService $productSearchService,
        PaginatorService $paginatorService,
        MovementHistoryRepository $movementHistoryRepository,
        OrbProductRepository $orbProductRepository,
        FournisseurRepository $fournisseurRepository
    ): Response {
        $productSearchResults = $orbData = $stockByEan = $facets = [];
        $paginator = null;
        $hideProductsNotFoundNotice = $isFrench = false;

        try {
            $form = $this->createForm(ProductSearchType::class)
                ->handleRequest($request);
        } catch (\Exception $e) {
            $this->get('ekino.new_relic.interactor')->noticeException($e);
            $this->addFlash('error', 'Erreur lors de la création du formulaire de recherche produit.');
            return $this->redirectToRoute('dashboard');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $page = $request->query->getInt('page', 1);
                $resultsPerPage = $request->query->getInt('resultsPerPage', 10);
                $totalResultsOnly = $request->query->get('totalResultsOnly', false);
                $isFrench = $request->query->getBoolean('is_french');

                $selectedFacets = $this->getSelectedFacets($request);
                $search = $this->getFormDataAsArray($form);
                $priceRange = [
                    'price_minimum' => $request->query->get('price_minimum'),
                    'price_maximum' => $request->query->get('price_maximum')
                ];
                $isFrenchArray = [
                    'is_french' => $isFrench
                ];

                $search = array_merge($search, $selectedFacets, $priceRange, $isFrenchArray);

                if ($totalResultsOnly) {
                    $result = $productSearchService->search($search, $page, $resultsPerPage, true);
                    return $this->json([
                        'totalResults' => $result['totalResults']
                    ]);
                }

                $orderBy = $request->query->get('orderBy', 'relevance');
                $orderDirection = $request->query->get('orderDirection', 'desc');

                $result = $productSearchService->search($search, $page, $resultsPerPage, false, $orderBy, $orderDirection);
                $productSearchResults = $result['productSearchResults'];

                foreach ($productSearchResults as $productResult) {
                    $productResult->setFournisseur(
                        $fournisseurRepository->findFournisseurPrincipal($productResult->getEan())
                    );
                }

                $facets = $result['facets'];

                if (count($productSearchResults) > 0) {
                    $eans = array_map(function ($p) {
                        return $p->getEan();
                    }, $productSearchResults);
                    $stockByEan = $movementHistoryRepository->findCurrentStockForEans(
                        $eans,
                        $this->getUser()->getSite()
                    );
                    $products = $orbProductRepository->getProducts($eans);
                    $orbData = array_combine(array_column($products, 'ean13'), $products);
                }

                $paginator = $paginatorService->createPaginator(
                    $result['totalResults'],
                    $resultsPerPage,
                    $page,
                    $request
                );
            } catch (ProductSearchException $e) {
                $this->get('ekino.new_relic.interactor')->noticeException($e);
                $this->addFlash('error', $e->getMessage());
                $hideProductsNotFoundNotice = true;
            } catch (\Solarium\Exception\HttpException $e) {
                $this->get('ekino.new_relic.interactor')->noticeException($e);
                $this->addFlash('error', "Erreur : le serveur de recherche n'a pas répondu.");
            } catch (\Exception $e) {
                $this->get('ekino.new_relic.interactor')->noticeException($e);
                $this->addFlash('error', 'Erreur lors de la recherche de produit.');
            }
        }

        $orderByChoices = [
            'relevance' => [
                'asc' => 'Pertinence'
            ],
            'published_at' => [
                'desc' => 'Date de parution (décroissante)',
                'asc' => 'Date de parution (croissante)'
            ],
            'price' => [
                'desc' => 'Prix (décroissant)',
                'asc' => 'Prix (croissant)'
            ],
            'product_title_sort' => [
                'asc' => 'Titre A à Z'
            ],
            'publisher_name' => [
                'asc' => 'Editeur A à Z'
            ],
        ];

        return $this->render('product_templates/search/index.html.twig', [
            'form' => $form->createView(),
            'productSearchResults' => $productSearchResults,
            'paginator' => $paginator,
            'orbData' => $orbData,
            'stockByEan' => $stockByEan,
            'facets' => $facets,
            'hideProductsNotFoundNotice' => $hideProductsNotFoundNotice,
            'orderByChoices' => $orderByChoices,
            'isFrenchSelected' => $isFrench,
        ]);
    }

    private function getFormDataAsArray(Form $form): array
    {
        $data = $form->getData();

        $publishedAtData = $form->get('published_at')->getData();

        $families = $data['family'];
        $family = $families['family']['family'];
        $subFamily = $families['sub_family']['family'];
        $subSubFamily = $families['sub_sub_family']['family'];

        return [
            'ean' => $data['ean'],
            'product_title' => $data['product_title'],
            'author_name' => $data['author_name'],
            'publisher_id' => $data['publisher_id'],
            'publisher_name' => $data['publisher_name'],
            'collection_id' => $data['collection_id'],
            'collection_name' => $data['collection_name'],
            'published_at_start' => $publishedAtData['date_debut'],
            'published_at_end' => $publishedAtData['date_fin'],
            'family_code' => $family ? $family->getId() : null,
            'sub_family_code' => $subFamily ? $subFamily->getId() : null,
            'sub_sub_family_code' => $subSubFamily ? $subSubFamily->getId() : null,
        ];
    }

    /**
     * Retourne les facets avec leurs valeurs si elles sont définis
     */
    private function getSelectedFacets(Request $request): array
    {
        $selectedFacets = [];
        foreach (ProductSearchService::FACETS as $facet) {
            $selectedFacets[$facet] = $request->query->get($facet);
        }

        return $selectedFacets;
    }
}
