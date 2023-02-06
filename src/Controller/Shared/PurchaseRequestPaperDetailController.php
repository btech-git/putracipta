<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseOrderPaperDetail;
use App\Grid\Shared\PurchaseRequestPaperDetailGridType;
use App\Repository\Transaction\PurchaseRequestPaperDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/purchase_request_paper_detail')]
class PurchaseRequestPaperDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_purchase_request_paper_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseRequestPaperDetailRepository $purchaseRequestPaperDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseRequestPaperDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseRequestPaperDetails) = $purchaseRequestPaperDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $sub = $new(PurchaseOrderPaperDetail::class, 'h');
            $sub->andWhere("IDENTITY(h.purchaseRequestPaperDetail) = {$alias}.id");
            $qb->leftJoin("{$alias}.purchaseOrderPaperDetail", 'd');
            $qb->andWhere($qb->expr()->orX('d.isCanceled = true', $qb->expr()->not($qb->expr()->exists($sub->getDQL()))));
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->innerJoin("{$alias}.paper", 'p');
            
            if (isset($request->query->get('purchase_request_paper_detail_grid')['filter']['paper:name']) && isset($request->query->get('purchase_request_paper_detail_grid')['sort']['paper:name'])) {
                $add['filter']($qb, 'p', 'name', $request->query->get('purchase_request_paper_detail_grid')['filter']['paper:name']);
                $add['sort']($qb, 'p', 'name', $request->query->get('purchase_request_paper_detail_grid')['sort']['paper:name']);
            }
            
            if (isset($request->query->get('purchase_request_paper_detail_grid')['filter']['paper:length']) && isset($request->query->get('purchase_request_paper_detail_grid')['sort']['paper:length'])) {
                $add['filter']($qb, 'p', 'length', $request->query->get('purchase_request_paper_detail_grid')['filter']['paper:length']);
                $add['sort']($qb, 'p', 'length', $request->query->get('purchase_request_paper_detail_grid')['sort']['paper:length']);
            }
            
            if (isset($request->query->get('purchase_request_paper_detail_grid')['filter']['paper:width']) && isset($request->query->get('purchase_request_paper_detail_grid')['sort']['paper:width'])) {
                $add['filter']($qb, 'p', 'width', $request->query->get('purchase_request_paper_detail_grid')['filter']['paper:width']);
                $add['sort']($qb, 'p', 'width', $request->query->get('purchase_request_paper_detail_grid')['sort']['paper:width']);
            }
            
            if (isset($request->query->get('purchase_request_paper_detail_grid')['filter']['paper:weight']) && isset($request->query->get('purchase_request_paper_detail_grid')['sort']['paper:weight'])) {
                $add['filter']($qb, 'p', 'weight', $request->query->get('purchase_request_paper_detail_grid')['filter']['paper:weight']);
                $add['sort']($qb, 'p', 'weight', $request->query->get('purchase_request_paper_detail_grid')['sort']['paper:weight']);
            }
            
            if (isset($request->query->get('purchase_request_paper_detail_grid')['filter']['unit:name']) && isset($request->query->get('purchase_request_paper_detail_grid')['sort']['unit:name'])) {
                $qb->innerJoin("{$alias}.unit", 'u');
                $add['filter']($qb, 'u', 'name', $request->query->get('purchase_request_paper_detail_grid')['filter']['unit:name']);
                $add['sort']($qb, 'u', 'name', $request->query->get('purchase_request_paper_detail_grid')['sort']['unit:name']);
            }
        });

        return $this->renderForm("shared/purchase_request_paper_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestPaperDetails' => $purchaseRequestPaperDetails,
        ]);
    }
}
