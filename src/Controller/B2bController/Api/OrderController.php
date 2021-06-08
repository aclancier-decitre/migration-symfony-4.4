<?php

namespace App\Controller\B2bController\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Exception;

class OrderController extends AbstractController
{

    /**
     * @param $ean
     * @param Request $request
     * @return JsonResponse
     * @throws Exception
     */
    public function updateQuantitiesAction($ean, Request $request)
    {
        $updatedQtes = $request->get("updated-quantity-b2b", array());
        return new JsonResponse($this->get("office.b2b.repository")->updateQuantities($ean, $updatedQtes));
    }
}
