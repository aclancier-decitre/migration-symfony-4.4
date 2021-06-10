<?php

namespace App\Service;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

class PaginatorService
{
    /**
     * @var Environment
     */
    private $twig;

    /** @var array */
    private $viewData;

    /** @var SlidingPagination */
    private $pagination;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function createPaginatorWithQueryParameters(
        int $totalResults,
        int $resultsPerPage,
        int $page,
        array $queryParameters,
        string $route,
        string $pageParameterName = "page"
    ): self {
        $params = [];
        $this->pagination = new SlidingPagination($params);
        $this->pagination->setTotalItemCount($totalResults);
        $this->pagination->setItemNumberPerPage($resultsPerPage);
        $this->pagination->setCurrentPageNumber($page);

        $this->viewData = $this->pagination->getPaginationData();
        $this->viewData['route'] = $route;
        $this->viewData['query'] = $queryParameters;
        $this->viewData['pageParameterName'] = $pageParameterName;

        return $this;
    }

    public function createPaginator(
        int $totalResults,
        int $resultsPerPage,
        int $page,
        Request $request,
        string $route = null
    ): self {
        $attributes = $request->attributes->all();

        return self::createPaginatorWithQueryParameters(
            $totalResults,
            $resultsPerPage,
            $page,
            array_merge($request->query->all(), $attributes['_route_params']),
            $route ?? $request->get('_route')
        );
    }

    public function render(): string
    {
        return $this->twig->render(
            'KnpPaginatorBundle:Pagination:twitter_bootstrap_v4_pagination.html.twig',
            $this->viewData
        );
    }

    public function getResultsPerPage(): int
    {
        return $this->pagination->getItemNumberPerPage();
    }

    public function getTotalResultsCount(): int
    {
        return $this->pagination->getTotalItemCount();
    }
}
