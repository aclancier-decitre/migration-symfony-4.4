<?php

namespace App\Repository\ProductRepository;

use App\Services\SolrService;
use App\Services\Tools\DatabaseDateHelper;
use App\Services\Exception\DatabaseException;
use App\Services\Exception\DatabaseActionException;
use App\Entity\ProductEntity\DonneesProduitLn;
use App\Entity\ProductEntity\Produit;
use App\Repository\ProductRepository\AuthorRepository;
use App\Repository\ProductRepository\PublisherRepository;
use App\Entity\ProductEntity\Product;
use App\Services\Factory\ProductFactory;
use App\Entity\ProductEntity\ProductType;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Driver\Connection;
use Solarium\QueryType\Select\Query\Query;

class ProductRepository
{
    private Connection $connection;

    private SolrService $solrService;

    private ProductFactory $productFactory;

    private PublisherRepository $editeurRepo;

    private AuthorRepository $auteurRepo;

    public function __construct(
        Connection $connection,
        SolrService $solrService,
        ProductFactory $productFactory,
        PublisherRepository $editeurRepo,
        AuthorRepository $auteurRepo
    ) {
        $this->connection = $connection;
        $this->solrService = $solrService;
        $this->productFactory = $productFactory;
        $this->editeurRepo = $editeurRepo;
        $this->auteurRepo = $auteurRepo;
    }

    /**
     * @return bool true si le produit existe dans la table produit, faux sinon
     */
    public function productExists(string $ean): bool
    {
        $sql = "SELECT 1 FROM produit WHERE cdpdt = :ean";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'ean' => $ean
        ]);

        return $stmt->fetchColumn(0) == 1;
    }

    public function searchTitle(string $term, int $limit = 50): array
    {
        $this->solrService->setCore(SolrService::CORE_PRODUCTS)->createSolrClient();
        /** @var Query $query */
        $query = $this->solrService->createQuery();

        $edismax = $query->getEDisMax();
        $edismax->setQueryFields('titre');

        $query->setQuery($term)
            ->setRows($limit);

        $documents = $this->solrService->getSolrClient()->select($query);

        $results = [];
        foreach ($documents as $document) {
            $results[] = [
                'title' => $document['titre'],
                'ean13' => $document['ean13'],
            ];
        }
        return $results;
    }

    public function searchStartingWithEan(string $ean, int $limit = 5): array
    {
        $this->solrService->setCore(SolrService::CORE_PRODUCTS)->createSolrClient();
        /** @var Query $query */
        $query = $this->solrService->createQuery();

        $queryString = sprintf('ean13:(%s*)', $ean);

        $query->setQuery($queryString)
            ->setRows($limit);

        return $this->solrService->getSolrClient()->select($query)->getDocuments();
    }

    public function getCurrentOrdersQuantities(string $siteCode, string $ean): array
    {
        $sql = "SELECT * FROM rc_info_stock_rch_pdt(:siteCode, :ean)";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([
            'siteCode' => $siteCode,
            'ean' => $ean
        ]);

        $data = $stmt->fetch();
        $currentOrdersQuantities = [
            'ean' => $data['cdpdt_ret'],
            'product_title' => $data['libpdt_ret'],
            'transfers_from_warehouse_amount' => $data['qtetrsf_ret'],
            'supplier_orders_amount' => $data['qtecdefour_ret'],
            'client_reservations_amount' => $data['qtecdeclient_ret'],
            'transfers_from_store_amount' => $data['qtetrsfmag_ret'],
            'last_supplier_order_at' => DatabaseDateHelper::getDateFromString($data['dtcrcdefour_ret']),
        ];
        return $currentOrdersQuantities;
    }

    /**
     * On récupère le détail d'un produit
     * @param string $ean
     * @param array|null $champs
     * @return Product
     * @throws DatabaseActionException
     * @throws DatabaseException
     */
    public function getInfosProduit(string $ean, ?array $champs = []): Product
    {
        $qb = $this->connection->createQueryBuilder();
        $qb->select(
            "p.cdpdt AS ean",
            "p.libpdt AS titre",
            "p.dtann AS dtann",
            "p.cdprecre AS cdprecre",
            "p.cdisbnpdt AS isbn",
            "p.dtparu AS date_parution",
            "p.nbpages AS nombre_pages",
            "p.pdspdt AS poids"
        )
            ->from("produit", "p")
            ->where(
                "p.cdpdt = :ean"
            );

        foreach ($champs as $champ) {
            switch ($champ) {
                case 'familles':
                    $qb->addSelect(
                        'p.cdfam AS code_famille, 
                        p.cdsfam AS code_sous_famille, 
                        p.cdssfam AS code_sous_sous_famille,
                        f.libfam AS libelle_famille, 
                        sf.libsfam AS libelle_sous_famille, 
                        ssf.libssfam AS libelle_sous_sous_famille'
                    )
                        ->leftJoin('p', 'famille', 'f', 'f.cdfam = p.cdfam')
                        ->leftJoin('p', 'sous_famille', 'sf', 'sf.cdsfam = p.cdsfam')
                        ->leftJoin('p', 'sous_sous_famille', 'ssf', 'ssf.cdssfam = p.cdssfam');
                    break;
                case 'resume':
                    $qb->addSelect('ptf_resume.contenu AS resume')
                        ->leftJoin(
                            'p',
                            'pdt_typefichier',
                            'ptf_resume',
                            "ptf_resume.cdpdt = p.cdpdt AND ptf_resume.cdtypfichier = 'RE'"
                        );
                    break;
                case 'biographie':
                    $qb->addSelect('ptf_biographie.contenu AS biographie')
                        ->leftJoin(
                            'p',
                            'pdt_typefichier',
                            'ptf_biographie',
                            "ptf_biographie.cdpdt = p.cdpdt AND ptf_biographie.cdtypfichier = 'BI'"
                        );
                    break;

                case 'auteur':
                    $qb->addSelect(
                        "json_agg(json_build_object(
                            'prenom', a.prenomauteur,
                            'nom', a.nomauteur,
                            'fonction', trim(fonc.libfoncminus),
                            'is_principal', pa.cdauteurprinc,
                            'ordre', pa.numordre
                        )) AS auteur"
                    )
                        ->leftJoin('p', 'pdt_auteur', 'pa', 'pa.cdpdt = p.cdpdt')
                        ->leftJoin('pa', 'fonction', 'fonc', 'pa.cdfonc = fonc.cdfonc')
                        ->innerJoin('pa', 'auteur', 'a', 'a.numaut = pa.numaut')
                        ->groupBy('p.cdpdt, p.libpdt, p.cdpdt, p.dtann, p.cdprecre, p.cdisbnpdt, p.dtparu, p.nbpages,
                        p.pdspdt, p.cdfam, p.cdsfam, p.cdssfam, f.libfam, sf.libsfam, ssf.libssfam, ptf_resume.contenu,
                        ptf_biographie.contenu, pdp.pvttc, tva.libtva, four.cdfour, four.nomfour, e.cdeditr, e.nomeditr,
                         rev.nomserieminus, collec.nomcollecminus, ed.libedition, me.libmarqueeditorialemin,
                         format.libformat, pres.libpresent, p.largeurpdt, p.hauteurpdt, p.epaisseurpdt, d.libdispo,
                         p.dtmajcddispo, rf.librepfourcde, prf.dtrepfour');
                    break;

                case 'prix':
                    $qb->addSelect(
                        "pdp.pvttc AS prix_ttc, tva.libtva AS libelle_taux_tva"
                    )
                        ->leftJoin(
                            'p',
                            'pdt_dateprix',
                            'pdp',
                            'pdp.cdpdt = p.cdpdt AND CURRENT_DATE BETWEEN pdp.dtdebvali1 AND pdp.dtfinval'
                        )
                        ->leftJoin(
                            'p',
                            'pdt_tva',
                            'ptva',
                            'ptva.cdpdt = p.cdpdt AND CURRENT_DATE BETWEEN ptva.dtdebvali1 AND ptva.dtfinval'
                        )
                        ->leftJoin(
                            'p',
                            'tva',
                            'tva',
                            'tva.cdtva = ptva.cdtva'
                        );
                    break;

                case 'fournisseur':
                    $qb->addSelect(
                        "four.cdfour AS code_fournisseur, four.nomfour AS nom_fournisseur"
                    )
                        ->leftJoin(
                            'p',
                            'pdt_four',
                            'pf',
                            'pf.cdpdt = p.cdpdt
                           AND CURRENT_DATE BETWEEN pf.dtdebvali1 AND pf.dtfinval AND pf.cdfourprinc
                           AND (pf.dtdebbloc IS NULL OR CURRENT_DATE < pf.dtdebbloc)'
                        )
                        ->leftJoin('pf', 'fournisseur', 'four', 'four.cdfour = pf.cdfour');
                    break;
                case 'editeur':
                    $qb->addSelect("e.cdeditr AS code_editeur, e.nomeditr AS nom_editeur")
                        ->leftJoin(
                            'p',
                            'editeur',
                            'e',
                            'e.cdeditr = p.cdeditr'
                        );
                    break;
                case 'serie-revue':
                    $qb->addSelect("rev.nomserieminus AS libelle_serie")
                        ->leftJoin(
                            'p',
                            'revue_serie',
                            'rev',
                            'rev.numserie = p.numserie'
                        );
                    break;
                case 'collection':
                    $qb->addSelect("collec.nomcollecminus AS libelle_collection")
                        ->leftJoin(
                            'p',
                            'collection',
                            'collec',
                            'collec.numcollec = p.numcollec'
                        );
                    break;
                case 'edition':
                    $qb->addSelect("ed.libedition AS libelle_edition")
                        ->leftJoin(
                            'p',
                            'edition',
                            'ed',
                            'ed.numedition = p.numedition'
                        );
                    break;

                case 'marque-editoriale':
                    $qb->addSelect("me.libmarqueeditorialemin AS libelle_marque_editoriale")
                        ->leftJoin(
                            'p',
                            'marque_editoriale',
                            'me',
                            'me.nummarqueeditoriale = p.nummarqueeditoriale'
                        );
                    break;

                case 'format':
                    $qb->addSelect("format.libformat AS libelle_format")
                        ->leftJoin(
                            'p',
                            'format',
                            'format',
                            'format.cdformat = p.cdformat'
                        );
                    break;

                case 'presentation':
                    $qb->addSelect("pres.libpresent AS libelle_presentation")
                        ->leftJoin(
                            'p',
                            'presentation',
                            'pres',
                            'pres.cdpresent = p.cdpresent'
                        );
                    break;

                case 'dimensions':
                    $qb->addSelect(
                        "p.largeurpdt AS largeur, 
                        p.hauteurpdt AS hauteur, 
                        p.epaisseurpdt AS epaisseur"
                    );
                    break;

                case 'disponibilite':
                    $qb->addSelect(
                        "d.libdispo AS libelle_disponibilite_dilicom,
                        p.dtmajcddispo AS date_maj_disponibilite_dilicom"
                    )
                        ->leftJoin('p', 'dispo_vdl', 'd', 'd.cddispo = p.cddispo');

                    if (in_array('fournisseur', $champs)) {
                        $qb->addSelect(
                            'rf.librepfourcde AS libelle_disponibilite_fournisseur,
                            prf.dtrepfour AS date_maj_disponibilite_fournisseur'
                        )
                            ->leftJoin(
                                'p',
                                'pdt_repfour',
                                'prf',
                                'prf.cdpdt = p.cdpdt AND prf.cdfour = pf.cdfour'
                            )
                            ->leftJoin(
                                'p',
                                'rep_four',
                                'rf',
                                'rf.cdrepfour = prf.cdrepfour'
                            );
                    }
                    break;
            }
        }

        $qb->setParameters(
            [
                ":ean" => $ean
            ]
        );

        try {
            $result = $qb->execute();
        } catch (Exception $e) {
            throw new DatabaseException($e, 'Erreur lors de la récupération des données produit');
        }

        $data = $result->fetch();

        if ($result->rowCount() != 1) {
            throw new DatabaseActionException(
                null,
                DatabaseActionException::COULD_NOT_FIND_PRODUCT,
                404
            );
        }
        return $this->productFactory->createFromArrayDetails($data);
    }

    public function getProduit(
        string $ean,
        ?string $codeSite = null
    ): Produit {
        $sql = "SELECT
                    cdpdt_ret AS ean,
                    libpdtminus_ret AS libelle_produit,
                    cdtyppdt_ret AS code_type_produit,
                    cdfam_ret AS code_famille,
                    cdsfam_ret AS code_sous_famille,
                    cdssfam_ret AS code_sous_sous_famille,
                    pvttc_ret AS prix_ttc,
                    pvht_ret AS prix_ht,
                    txtva_ret AS taux_tva,
                    cdtva_ret AS code_tva,
                    libtva_ret AS libelle_tva,
                    cdprecre_ret AS is_precre,
                    cdaparaitre_ret AS is_a_paraitre,
                    cdreference_ret AS is_reference,
                    cddispo_ret AS code_dispo,
                    cdpvfixedecitre_ret AS is_prix_fixe_decitre,
                    cdfour_ret AS code_fournisseur,
                    cdpdtfour_ret AS code_produit_fournisseur,
                    nomfour_ret AS nom_fournisseur,
                    idediteur_ret AS id_editeur,
                    numauteur_ret AS id_auteur,
                    cdmodtsm_ret AS code_mode_transmission,
                    libmodtsm_ret AS libelle_mode_transmission,
                    dtann_ret AS date_annulation,
                    dtparu_ret AS date_parution,
                    dtdebbloc_ret AS date_debut_bloquage,
                    dtcr_ret AS date_creation,
                    qteenstock_ret AS quantite_en_stock,
                    cde_ln_bloquee_ret AS commande_ln_bloquee,
                    qtecondach AS quantite_condition_achat,
                    cdstogest AS is_gestion_stock_central
                FROM web_cde_part_pdt_infos_s(:ean, :codeSite)";

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                'ean' => $ean,
                'codeSite' => $codeSite,
            ]);
        } catch (Exception $e) {
            throw new DatabaseException($e, 'Erreur lors de la récupération des données produit');
        }

        $data = $stmt->fetch();

        $dateAnnulation = null;
        if ($data['date_annulation']) {
            $dateAnnulation = new \DateTime($data['date_annulation']);
        }

        $dateParution = null;
        if ($data['date_parution']) {
            $dateParution = new \DateTime($data['date_parution']);
        }

        $dateDebutBloquage = null;
        if ($data['date_debut_bloquage']) {
            $dateDebutBloquage = new \DateTime($data['date_debut_bloquage']);
        }

        $dateCreation = null;
        if ($data['date_creation']) {
            $dateCreation = new \DateTime($data['date_creation']);
        }

        $editeur = null;
        if ($editeurId = $data['id_editeur']) {
            $editeur = $this->editeurRepo->findOneById($editeurId);
        }
        $auteur = null;
        if ($auteurId = $data['id_auteur']) {
            $auteur = $this->auteurRepo->findOneById($auteurId);
        }


        $produit = new Produit(
            $data['ean'],
            $data['code_type_produit'],
            $data['is_precre'],
            $data['is_a_paraitre'],
            $data['is_reference'],
            $data['commande_ln_bloquee'],
            $data['is_gestion_stock_central'],
            $data['quantite_en_stock'],
            $data['is_prix_fixe_decitre'],
            $data['libelle_produit'],
            $data['code_famille'],
            $data['code_sous_famille'],
            $data['code_sous_sous_famille'],
            $data['prix_ttc'],
            $data['prix_ht'],
            $data['taux_tva'],
            $data['code_tva'],
            $data['libelle_tva'],
            $data['code_dispo'],
            $data['code_fournisseur'],
            $data['code_produit_fournisseur'],
            $data['nom_fournisseur'],
            $editeur,
            $auteur,
            $data['code_mode_transmission'],
            $data['libelle_mode_transmission'],
            $dateAnnulation,
            $dateParution,
            $dateDebutBloquage,
            $dateCreation,
            $data['quantite_condition_achat']
        );

        if ($data['code_type_produit'] === ProductType::CODE_PRODUIT_LN) {
            $donneesProduitLn = $this->getDonneesProduitLn($data['ean']);
            $produit->setDonneesProduitLn($donneesProduitLn);
        }

        return $produit;
    }

    /**
     * @return DonneesProduitLn[]
     */
    public function getDonneesProduitLn(string $ean): array
    {
        $sql = "SELECT
                    sku_ret AS sku,
                    cdformatln_ret AS code_format_ln,
                    libformatln_ret AS libelle_format_ln,
                    pvttc_ret AS pv_ttc,
                    cddispoln_ret AS code_dispo_ln,
                    libdispoln_ret AS libelle_dispo_ln,
                    cdplateformeln_ret AS code_plateforme_ln
                FROM web_cde_part_pdt_donnees_ln_s(:ean)";

        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([
                'ean' => $ean
            ]);

            $donneesProduitLn = [];
            while ($data = $stmt->fetch()) {
                $donneesProduitLn[] = new DonneesProduitLn(
                    $data['sku'],
                    $data['code_format_ln'],
                    $data['libelle_format_ln'],
                    $data['pv_ttc'],
                    $data['code_dispo_ln'],
                    $data['libelle_dispo_ln'],
                    $data['code_plateforme_ln']
                );
            }
            return $donneesProduitLn;
        } catch (Exception $e) {
            throw new DatabaseException($e, 'Erreur lors de la récupération des données LN du produit');
        }
    }
}
