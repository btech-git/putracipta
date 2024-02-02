<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Stock\InventoryProductReceiveHeader;
use App\Grid\Production\MasterOrderHeaderGridType;
use App\Repository\Production\MasterOrderHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/master_order_header')]
class MasterOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_master_order_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MasterOrderHeaderRepository $masterOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(MasterOrderHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $masterOrderHeaders) = $masterOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $qb->andWhere("{$alias}.isCanceled = false");
//            $qb->andWhere("{$alias}.totalRemainingProduction > 0");
        });

        return $this->renderForm("shared/master_order_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'masterOrderHeaders' => $masterOrderHeaders,
        ]);
    }
}
