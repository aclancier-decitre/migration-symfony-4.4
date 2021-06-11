<?php

namespace App\Entity\RepriseEntity;

use DateTime;
use JMS\Serializer\Annotation\HandlerCallback;
use JMS\Serializer\JsonSerializationVisitor;

class Livre
{
    const POIDS_DEFAUT = 0.4;

    const DATE_PARUTION_MIN = '2004-01-01';

    private string $titre;

    private string $ean;

    private float $poids;

    private ?DateTime $dateParution;

    private string $codeFamille;

    protected float $prixAuKilo = 2;

    /**
     * @return string
     */
    public function getTitre()
    {
        return $this->titre;
    }

    /**
     * @param string $titre
     *
     * @return Livre
     */
    public function setTitre($titre)
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * Poids en grammes
     *
     * @return int
     */
    public function getPoids()
    {
        return $this->poids;
    }

    /**
     * @param float $poids
     *
     * @return Livre
     */
    public function setPoids($poids)
    {
        $this->poids = $poids;

        return $this;
    }

    /**
     * @return string
     */
    public function getEan()
    {
        return $this->ean;
    }

    /**
     * @param string $ean
     *
     * @return Livre
     */
    public function setEan($ean)
    {
        $this->ean = $ean;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateParution()
    {
        return $this->dateParution;
    }

    /**
     * @param \DateTime|null $dateParution
     *
     * @return $this
     */
    public function setDateParution(\DateTime $dateParution = null)
    {
        $this->dateParution = $dateParution;

        return $this;
    }

    /**
     * @return string
     */
    public function getCodeFamille()
    {
        return $this->codeFamille;
    }

    /**
     * @param string $codeFamille
     *
     * @return $this
     */
    public function setCodeFamille($codeFamille)
    {
        $this->codeFamille = $codeFamille;

        return $this;
    }

    /**
     * @return bool
     */
    public function isReprisDecitre()
    {
        $rulesResults = $this->getRepriseDecitreRulesResults();
        return $rulesResults['status'];
    }

    /**
     * @return string
     */
    public function getReprisDecitreErreur()
    {
        $rulesResults = $this->getRepriseDecitreRulesResults();
        return $this->getFailMessageFromResults($rulesResults);
    }

    /**
     * @return bool
     */
    public function isReprisFonds()
    {
        $rulesResults = $this->getRepriseFondsRulesResults();
        return $rulesResults['status'];
    }

    /**
     * @return string
     */
    public function getReprisFondsErreur()
    {
        $rulesResults = $this->getRepriseFondsRulesResults();
        return $this->getFailMessageFromResults($rulesResults);
    }

    /**
     * @return array
     */
    public function getRepriseDecitreRulesResults()
    {
        $rules = array(
            'code_famille' => array(
                'fail_message' => "Code famille non repris.",
                'status'       => in_array($this->getCodeFamille(), $this->getCodesFamillesRepris()),
            ),
            'date_parution' => array(
                'fail_message' => "Livre trop ancien (> 10 ans).",
                'status'       => $this->getDateParution() >= $this->getDateParutionMin(),
            ),
        );

        return array('rules' => $rules, 'status' => $this->getGlobalStatusFromRules($rules));
    }

    /**
     * @return array
     */
    public function getRepriseFondsRulesResults()
    {
        $rules = array(
            'code_famille' => array(
                'fail_message' => "Code famille non repris par le fonds.",
                'status'       => in_array($this->getCodeFamille(), $this->getCodesFamillesReprisFonds()),
            ),
        );

        return array('rules' => $rules, 'status' => $this->getGlobalStatusFromRules($rules));
    }

    /**
     * @param $rules
     * @return bool
     */
    protected function getGlobalStatusFromRules($rules)
    {
        $globalStatus = true;
        foreach ($rules as $rule) {
            if (!$rule['status']) {
                $globalStatus = false;
                break;
            }
        }

        return $globalStatus;
    }

    /**
     * @param $results
     * @return string
     */
    protected function getFailMessageFromResults($results)
    {
        if ($results['status']) {
            return '';
        }

        foreach ($results['rules'] as $rule) {
            if (!$rule['status']) {
                return $rule['fail_message'];
            }
        }
    }

    /**
     * @HandlerCallback("json", direction = "serialization")
     */
    public function jsonSerialize(JsonSerializationVisitor $visitor)
    {
        $root = array(
            'titre' => $this->getTitre(),
            'ean' => $this->getEan(),
            'poids' => $this->getPoids(),
            'code_famille' => $this->getCodeFamille(),
            'montant' => $this->getMontant(),
        );

        $root['is_repris_decitre'] = $this->isReprisDecitre();
        $root['is_repris_decitre_message'] = $this->getReprisDecitreErreur();

        $root['is_repris_fonds'] = $this->isReprisFonds();
        $root['is_repris_fonds_message'] = $this->getReprisFondsErreur();

        $visitor->setRoot($root);
    }

    /**
     * @return array
     */
    protected function getCodesFamillesRepris()
    {
        return array(
            //BD / Manga
            281, 282, 283, 284,
            //Beaux-arts
            140, 141, 142, 147, 148, 150,
            //Histoire
            210, 211, 212, 214, 215, 216,
            //Jeunesse,
            151, 152, 153, 155, 156, 158,
            //Littérature
            112, 113, 114, 115, 116, 117, 118, 288, 289, 290, 291, 292, 283, 293,
            //Sciences-humaines
            126, 260,
            //Tourisme
            124, 138, 145,
            //Vie pratique
            123, 125, 128, 130, 131, 134, 135, 136, 137, 139, 248
        );
    }

    /**
     * @return array
     */
    protected function getCodesFamillesReprisFonds()
    {
        return array(
            // Ouvrages de fiction et non-fiction pour les publics jeunesse et adulte
            115, 117, 118, 153, 155, 156, 288, 289, 290, 291, 292, 293,
            // Ouvrages documentaires dans tous les domaines (-10 ans)
            158,
            // Albums (texte abordable, très illustrés) dont albums sans texte et albums de première lecture
            151, 152,
            // Contes, dont contes étrangers ou en langues étrangères
            152, 153,
            // Romans, dont romans de première lecture
            155,
            // Tout type de bandes dessinées
            281, 282, 284,
            // Poésie
            116,
            // Sciences humaines et sociales : art, langues, philosophie, psychologie,
            // gestion, économie, histoire et géographie
            234, 260, 261, 262, 264, 210, 211, 212, 214, 215, 216,
            // Livres pratiques : sur la santé, l'hygiène, la puériculture, la famille, l'éducation, le sport,
            // la couture, les arts, l'élevage, l'agriculture, etc.
            123, 128, 260, 263, 248, 131, 135, 139,
            // Livres de cuisine
            134, 130,
            // Beaux livres
            136, 137, 138, 140, 141, 142, 145, 147,
            // Dico
            143,
        );
    }

    /**
     * @return float
     */
    public function getMontant()
    {
        if (!$this->isReprisDecitre()) {
            return 0;
        }

        $poids = $this->getPoids();
        if (!$poids) {
            $poids = self::POIDS_DEFAUT;
        }

        $montant = $this->getPrixAuKilo() * $poids;

        // Arrondi aux 5 centimes supérieurs
        $montant = ceil($montant / 0.05) * 0.05;

        return $montant;
    }

    /**
     * @param $prixAuKilo
     * @return $this
     */
    public function setPrixAuKilo($prixAuKilo)
    {
        $this->prixAuKilo = $prixAuKilo;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrixAuKilo()
    {
        return $this->prixAuKilo;
    }

    /**
     * @return \DateTime
     */
    private function getDateParutionMin()
    {
        return new \DateTime(self::DATE_PARUTION_MIN);
    }
}
