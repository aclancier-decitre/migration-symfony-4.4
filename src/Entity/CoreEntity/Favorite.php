<?php

namespace App\Entity\CoreEntity;

class Favorite
{

    private int $favoriteId;

    private string $cdoper;

    private string $label;

    private string $route;

    private ?array $routeParameters;

    /**
     * @return int
     */
    public function getFavoriteId(): int
    {
        return $this->favoriteId;
    }

    /**
     * @param int $favoriteId
     * @return $this
     */
    public function setFavoriteId(int $favoriteId)
    {
        $this->favoriteId = $favoriteId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCdoper(): string
    {
        return $this->cdoper;
    }

    /**
     * @param string $cdoper
     * @return $this
     */
    public function setCdoper(string $cdoper)
    {
        $this->cdoper = $cdoper;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $route
     * @return $this
     */
    public function setRoute(string $route)
    {
        $this->route = $route;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getRouteParameters()
    {
        return $this->routeParameters;
    }

    /**
     * @param array|null $routeParameters
     * @return $this
     */
    public function setRouteParameters($routeParameters)
    {
        $this->routeParameters = $routeParameters;
        return $this;
    }
}
