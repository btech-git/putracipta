<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\MaterialRequestHeaderGridType;
use App\Repository\Stock\MaterialRequestHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/material_request_header')]
class MaterialRequestHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_material_request_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MaterialRequestHeaderRepository $materialRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(MaterialRequestHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $materialRequestHeaders) = $materialRequestHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.totalQuantityRemaining > 0.00");
        });

        return $this->renderForm("shared/material_request_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'materialRequestHeaders' => $materialRequestHeaders,
        ]);
    }
}
