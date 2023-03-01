<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Grid\Production\MasterOrderGridType;
use App\Repository\Production\MasterOrderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/master_order')]
class MasterOrderController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_master_order__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MasterOrderRepository $masterOrderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'productionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(MasterOrderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $masterOrders) = $masterOrderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/master_order/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'masterOrders' => $masterOrders,
        ]);
    }
}
