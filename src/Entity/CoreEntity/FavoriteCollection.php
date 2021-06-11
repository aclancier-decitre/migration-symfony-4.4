<?php

namespace App\Entity\CoreEntity;

class FavoriteCollection
{

    private array $favorites;

    /**
     * @param array $favorites
     * @return $this
     */
    public function setFavorites(array $favorites)
    {
        $this->favorites = $favorites;
        return $this;
    }

    /**
     * @return array|null
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    /**
     * @param string $routeName
     * @param array $routeParams
     * @return mixed
     */
    public function getFavoriteIdByRouteAndParameters(string $routeName, array $routeParams)
    {
        foreach ($this->favorites as $favorite) {
            if ($routeName == $favorite->getRoute() &&
                count(array_diff_assoc($favorite->getRouteParameters(), $routeParams)) == 0
            ) {
                return $favorite->getFavoriteId();
            }
        }

        return null;
    }

    /**
     * @param Favorite $favorite
     * @return $this
     */
    public function addFavorite(Favorite $favorite)
    {
        $this->favorites[$favorite->getFavoriteId()] = $favorite;
        return $this;
    }

    /**
     * @param $favoriteId
     */
    public function deleteFavorite($favoriteId)
    {
        unset($this->favorites[$favoriteId]);
    }
}
