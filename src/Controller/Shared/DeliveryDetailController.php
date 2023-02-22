<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\SaleInvoiceDetail;
use App\Grid\Shared\DeliveryDetailGridType;
use App\Repository\Transaction\DeliveryDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/delivery_detail')]
class DeliveryDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_delivery_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DeliveryDetailRepository $deliveryDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(DeliveryDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $deliveryDetails) = $deliveryDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $customerId = '';
            $qb->innerJoin("{$alias}.deliveryHeader", 'd');
            $qb->innerJoin("{$alias}.product", 'p');
            $qb->innerJoin("{$alias}.unit", 'u');
            $qb->innerJoin("{$alias}.saleOrderDetail", 'sd');
            $qb->innerJoin("sd.saleOrderHeader", 'sh');
            
            if (isset($request->query->get('sale_invoice_header')['customer'])) {
                $customerId = $request->query->get('sale_invoice_header')['customer'];
            }
            if (!empty($customerId)) {
                $qb->andWhere("IDENTITY(d.customer) = :customerId");
                $qb->setParameter('customerId', $customerId);
            }
            
            if (isset($request->query->get('delivery_detail_grid')['filter']['product:code']) && isset($request->query->get('delivery_detail_grid')['sort']['product:code'])) {
                $add['filter']($qb, 'p', 'code', $request->query->get('delivery_detail_grid')['filter']['product:code']);
                $add['sort']($qb, 'p', 'code', $request->query->get('delivery_detail_grid')['sort']['product:code']);
            }
            
            if (isset($request->query->get('delivery_detail_grid')['filter']['product:name']) && isset($request->query->get('delivery_detail_grid')['sort']['product:name'])) {
                $add['filter']($qb, 'p', 'name', $request->query->get('delivery_detail_grid')['filter']['product:name']);
                $add['sort']($qb, 'p', 'name', $request->query->get('delivery_detail_grid')['sort']['product:name']);
            }
            
            if (isset($request->query->get('delivery_detail_grid')['filter']['unit:name']) && isset($request->query->get('delivery_detail_grid')['sort']['unit:name'])) {
                $add['filter']($qb, 'u', 'name', $request->query->get('delivery_detail_grid')['filter']['unit:name']);
                $add['sort']($qb, 'u', 'name', $request->query->get('delivery_detail_grid')['sort']['unit:name']);
            }
            
            if (isset($request->query->get('delivery_detail_grid')['filter']['saleOrderHeader:referenceNumber']) && isset($request->query->get('delivery_detail_grid')['sort']['saleOrderHeader:referenceNumber'])) {
                $add['filter']($qb, 'sh', 'referenceNumber', $request->query->get('delivery_detail_grid')['filter']['saleOrderHeader:referenceNumber']);
                $add['sort']($qb, 'sh', 'referenceNumber', $request->query->get('delivery_detail_grid')['sort']['saleOrderHeader:referenceNumber']);
            }
            
            $sub = $new(SaleInvoiceDetail::class, 's');
            $sub->andWhere("IDENTITY(s.deliveryDetail) = {$alias}.id");
            $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/delivery_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'deliveryDetails' => $deliveryDetails,
        ]);
    }
}
