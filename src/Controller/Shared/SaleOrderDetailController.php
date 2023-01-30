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

        list($count, $saleOrderDetails) = $saleOrderDetailRepository->fetchData($criteria, function($qb, $alias) use ($request) {
            $customerId = '';
            if (isset($request->query->get('delivery_header')['customer'])) {
                $customerId = $request->query->get('delivery_header')['customer'];
            }
            if (!empty($customerId)) {
                $qb->innerJoin("{$alias}.saleOrderHeader", 's');
                $qb->andWhere("IDENTITY(s.customer) = :customerId");
                $qb->setParameter('customerId', $customerId);
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
