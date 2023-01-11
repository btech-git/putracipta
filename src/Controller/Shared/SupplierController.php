<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\SupplierGridType;
use App\Repository\Master\SupplierRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/supplier')]
class SupplierController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_supplier__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, SupplierRepository $supplierRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(SupplierGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $suppliers) = $supplierRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isInactive = 0");
        });

        return $this->renderForm("shared/supplier/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'suppliers' => $suppliers,
        ]);
    }
}
