<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\PurchaseOrderPaperHeaderGridType;
use App\Repository\Transaction\PurchaseOrderPaperHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/purchase_order_paper_header')]
class PurchaseOrderPaperHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_purchase_order_paper_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseOrderPaperHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseOrderPaperHeaders) = $purchaseOrderPaperHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.totalRemainingReceive > 0");
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/purchase_order_paper_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
        ]);
    }
}
