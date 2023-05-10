<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseOrderDetail;
use App\Grid\Shared\PurchaseRequestDetailGridType;
use App\Repository\Transaction\PurchaseRequestDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/purchase_request_detail')]
class PurchaseRequestDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_purchase_request_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseRequestDetailRepository $purchaseRequestDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseRequestDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseRequestDetails) = $purchaseRequestDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $sub = $new(PurchaseOrderDetail::class, 'p');
            $sub->andWhere("IDENTITY(p.purchaseRequestDetail) = {$alias}.id");
            $qb->leftJoin("{$alias}.purchaseOrderDetail", 'd');
            $qb->andWhere($qb->expr()->orX('d.isCanceled = true', $qb->expr()->not($qb->expr()->exists($sub->getDQL()))));
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.transactionStatus = 'Approve'");
            
            if (isset($request->query->get('purchase_request_detail_grid')['filter']['material:name']) && isset($request->query->get('purchase_request_detail_grid')['sort']['material:name'])) {
                $qb->innerJoin("{$alias}.material", 'm');
                $add['filter']($qb, 'm', 'name', $request->query->get('purchase_request_detail_grid')['filter']['material:name']);
                $add['sort']($qb, 'm', 'name', $request->query->get('purchase_request_detail_grid')['sort']['material:name']);
            }
            
            if (isset($request->query->get('purchase_request_detail_grid')['filter']['unit:name']) && isset($request->query->get('purchase_request_detail_grid')['sort']['unit:name'])) {
                $qb->innerJoin("{$alias}.unit", 'u');
                $add['filter']($qb, 'u', 'name', $request->query->get('purchase_request_detail_grid')['filter']['unit:name']);
                $add['sort']($qb, 'u', 'name', $request->query->get('purchase_request_detail_grid')['sort']['unit:name']);
            }
        });

        return $this->renderForm("shared/purchase_request_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestDetails' => $purchaseRequestDetails,
        ]);
    }
}
