<?php

namespace App\Entity\CoreEntity;

use App\Entity\CoreEntity\Product;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{

    protected string $login;

    protected ?string $password;

    protected string $nom;

    protected string $prenom;

    protected array $profils;

    protected string $siteId;

    protected array $roles;

    /**
     * @var FavoriteCollection
     */
    protected $favorites = null;

    protected array $queryList;

    protected bool $isExpired = false;

    protected array $manageableFamilies;

    /**
     * @return FavoriteCollection
     */
    public function getFavorites()
    {
        return $this->favorites;
    }

    /**
     * @param FavoriteCollection $favorites
     * @return $this
     */
    public function setFavorites(FavoriteCollection $favorites)
    {
        $this->favorites = $favorites;
        return $this;
    }

    /**
     * @return \Symfony\Component\Security\Core\Role\Role[]|void
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return string|void
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return null
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getLogin();
    }

    /**
     * Removes sensitive data from the user.
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->password = null;
    }

    /**
     * @param string $login
     * @return $this
     */
    public function setLogin($login)
    {
        $this->login = $login;

        return $this;
    }

    /**
     * @param string $password
     * @return $this
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     * @return $this
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     * @return $this
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @return array
     */
    public function getProfils()
    {
        return $this->profils;
    }

    /**
     * @param array $profils
     * @return $this
     */
    public function setProfils($profils)
    {
        $this->profils = $profils;

        return $this;
    }

    /**
     * @param string $siteId
     * @return $this
     */
    public function setSiteId($siteId)
    {
        $this->siteId = $siteId;

        return $this;
    }

    /**
     * @return mixed|string
     * @throws \LogicException
     */
    public function getSiteId()
    {
        $profils = $this->getProfils();

        if ($this->siteId) {
            if (isset($profils[$this->siteId])) {
                return $this->siteId;
            } else {
                throw new \LogicException("Le site défini n'est pas dans les profils");
            }
        } elseif (count($profils) === 1) {
            return key($profils);
        } else {
            throw new \LogicException("Le site par défaut n'a pas été défini");
        }
    }

    /**
     * Défini l'id du site en prenant le premier disponible.
     */
    public function activeDefaultSite()
    {
        $this
            ->setSiteId(
                key(
                    $this
                        ->getProfils()
                )
            );
    }

    /**
     * Renvoie l'id du site mais sans risque d'exception
     * @return null|string
     */
    public function getSite()
    {
        try {
            return $this->getSiteId();
        } catch (\LogicException $e) {
            return null;
        }
    }

    /**
     * @return bool
     */
    public function hasSiteId()
    {
        try {
            $this->getSiteId();
            return true;
        } catch (\LogicException $e) {
            return false;
        }
    }

    /**
     * @return bool
     */
    public function needSiteSelection()
    {
        return !$this->hasSiteId();
    }

    /**
     * @return string
     */
    public function getSiteLibelle()
    {
        $sites = $this->getAllSites();

        $site = $this->getSite();

        return isset($sites[$site]) ? $sites[$site] : $site;
    }

    public function getAvailableSites()
    {
        $sites = $this->getAllSites();
        $profils = $this->getProfils();

        $availableSites = array();
        foreach (array_keys($profils) as $siteId) {
            $siteName = isset($sites[$siteId]) ? $sites[$siteId] : $siteId;
            $availableSites[$siteId] = $siteName;
        }

        return $availableSites;
    }

    public function getAllSites()
    {
        return array(
            'ANN'  => 'Annecy',
            'ANM'  => 'Annemasse',
            'BAT'  => 'Logistique',
            'BEL'  => 'Bellecour',
            'CHA'  => 'Chambéry',
            'COL'  => 'Service bibliothèque',
            'CPTA' => 'Service comptabilité',
            'BVS'  => 'Beauvais',
            'CONF' => 'Confluence',
            'ECU'  => 'Ecully',
            'MRK'  => 'Service marketing',
            'GRE'  => 'Grenoble',
            'INFO' => 'Service informatique',
            'JUN'  => 'Langues du monde',
            'DI'   => 'Decitre Interactive',
            'STG'  => 'Saint-Genis Laval',
            'SPR'  => 'Saint Priest',
            'STK'  => 'Stock central',
            'VILL' => 'Villiers',
            'LPA'  => 'La Part Dieu',
            'ENT'  => 'Entrepôt Bataille',
            'GRL'  => 'Magasin éphémère Grenoble',
            'SOO'  => 'So Ouest',
        );
    }

    /**
     * @return mixed
     */
    public function getQueryList()
    {
        return $this->queryList;
    }

    /**
     * @param array $queryList
     * @return $this
     */
    public function setQueryList(array $queryList)
    {
        $this->queryList = $queryList;
        return $this;
    }

    /**
     * @param int $queryId
     * @return mixed
     */
    public function getQueryById(int $queryId)
    {
        foreach ($this->queryList as $query) {
            if ($query->getQueryId() == $queryId) {
                return $query;
            }
        }
        return null;
    }

    /**
     * @return |null
     */
    public function getIsExpired() : bool
    {
        return $this->isExpired;
    }

    /**
     * @return $this
     */
    public function setIsExpired(bool $isExpired) : self
    {
        $this->isExpired = $isExpired;
        return $this;
    }

    /**
     * @param array $manageableFamilies
     * @return User
     */
    public function setManageableFamilies(array $manageableFamilies): self
    {
        $this->manageableFamilies = $manageableFamilies;
        return $this;
    }

    public function getManageableFamilies(): array
    {
        return $this->manageableFamilies;
    }

    public function getManageableFamiliesCodes(): array
    {
        return array_column($this->manageableFamilies, 'code');
    }

    public function addFavorite(Favorite $favorite): self
    {
        $this->getFavorites()->addFavorite($favorite);
        return $this;
    }

    public function removeFavorite(int $id): self
    {
        $this->getFavorites()->deleteFavorite($id);
        return $this;
    }
}
