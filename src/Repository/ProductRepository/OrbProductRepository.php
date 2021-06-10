<?php

namespace App\Repository\ProductRepository;

class OrbProductRepository
{
    const API_TIMEOUT = 60;

    const API_MAX_PRODUCTS = 500;

    private string $host;

    private string $username;

    private string $apiKey;

    public function __construct(string $host, string $username, string $apiKey)
    {
        $this->host = $host;
        $this->username = $username;
        $this->apiKey = $apiKey;
    }


    public function getProduct(string $ean13): array
    {
        $products = $this->getProducts([$ean13]);
        return array_shift($products);
    }

    /**
     * @return mixed
     */
    public function getProducts(array $ean13)
    {
        if (0 === count($ean13)) {
            return [];
        }

        //L'api ORB a une limite de nombre de produits. Il faut donc potentiellement faire plusieurs appels à celle-ci
        $chunks = array_chunk($ean13, self::API_MAX_PRODUCTS);
        $products = [];
        foreach ($chunks as $chunk) {
            $products = array_merge($products, $this->callApi($chunk));
        }

        return $products;
    }

    private function callApi(array $ean13): array
    {
        $context = stream_context_create([
            'http' => [
                'header' =>
                    'Authorization: Basic ' . base64_encode($this->username . ':' . $this->apiKey)
                    // attention à bien utiliser ce séparateur \r\n sous peine de faire échouer l'appel
                    ."\r\nAccept-Encoding: deflate, gzip"
                ,
                'timeout' => self::API_TIMEOUT,
            ]
        ]);

        $url = sprintf(
            'https://%s/v1/products?eans=%s&limit=%d&sort=ean_asc',
            $this->host,
            implode(',', $ean13),
            count($ean13)
        );

        $apiContent = json_decode(file_get_contents('compress.zlib://' . $url, false, $context), true);

        if (!isset($apiContent['data'])) {
            throw new \RuntimeException("Pas de produit trouvé pour l'url " . $url);
        }

        return $apiContent['data'];
    }
}
