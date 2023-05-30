<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\SaleOrderDetailGridType;
use App\Repository\Transaction\SaleOrderDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/sale_order_detail')]
class SaleOrderDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_sale_order_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, SaleOrderDetailRepository $saleOrderDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(SaleOrderDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $saleOrderDetails) = $saleOrderDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $customerId = '';
            $qb->innerJoin("{$alias}.saleOrderHeader", 's');
            $qb->innerJoin("{$alias}.product", 'p');
            $qb->innerJoin("{$alias}.unit", 'u');
            
            if (isset($request->query->get('delivery_header')['customer'])) {
                $customerId = $request->query->get('delivery_header')['customer'];
            } elseif (isset($request->query->get('master_order_header')['customer'])) {
                $customerId = $request->query->get('master_order_header')['customer'];
            }
            if (!empty($customerId)) {
                $qb->andWhere("IDENTITY(s.customer) = :customerId");
                $qb->setParameter('customerId', $customerId);
            }
            
            if (isset($request->query->get('sale_order_detail_grid')['filter']['saleOrderHeader:referenceNumber']) && isset($request->query->get('sale_order_detail_grid')['sort']['saleOrderHeader:referenceNumber'])) {
                $add['filter']($qb, 's', 'referenceNumber', $request->query->get('sale_order_detail_grid')['filter']['saleOrderHeader:referenceNumber']);
                $add['sort']($qb, 's', 'referenceNumber', $request->query->get('sale_order_detail_grid')['sort']['saleOrderHeader:referenceNumber']);
            }
            
            if (isset($request->query->get('sale_order_detail_grid')['filter']['product:code']) && isset($request->query->get('sale_order_detail_grid')['sort']['product:code'])) {
                $add['filter']($qb, 'p', 'code', $request->query->get('sale_order_detail_grid')['filter']['product:code']);
                $add['sort']($qb, 'p', 'code', $request->query->get('sale_order_detail_grid')['sort']['product:code']);
            }
            
            if (isset($request->query->get('sale_order_detail_grid')['filter']['product:name']) && isset($request->query->get('sale_order_detail_grid')['sort']['product:name'])) {
                $add['filter']($qb, 'p', 'name', $request->query->get('sale_order_detail_grid')['filter']['product:name']);
                $add['sort']($qb, 'p', 'name', $request->query->get('sale_order_detail_grid')['sort']['product:name']);
            }
            
            if (isset($request->query->get('sale_order_detail_grid')['filter']['unit:name']) && isset($request->query->get('sale_order_detail_grid')['sort']['unit:name'])) {
                $add['filter']($qb, 'u', 'name', $request->query->get('sale_order_detail_grid')['filter']['unit:name']);
                $add['sort']($qb, 'u', 'name', $request->query->get('sale_order_detail_grid')['sort']['unit:name']);
            }
            $qb->andWhere("{$alias}.remainingDelivery > 0");
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/sale_order_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleOrderDetails' => $saleOrderDetails,
        ]);
    }
}
