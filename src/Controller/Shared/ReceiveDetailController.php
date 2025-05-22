<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Purchase\PurchaseInvoiceDetail;
use App\Grid\Shared\ReceiveDetailGridType;
use App\Repository\Purchase\ReceiveDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/receive_detail')]
class ReceiveDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_receive_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ReceiveDetailRepository $receiveDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ReceiveDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $receiveDetails) = $receiveDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $supplierId = '';
            $qb->innerJoin("{$alias}.receiveHeader", 'h');
            $qb->leftJoin("{$alias}.material", 'm');
            $qb->leftJoin("{$alias}.paper", 'p');
            
            if (isset($request->request->get('purchase_invoice_header')['supplier'])) {
                $supplierId = $request->request->get('purchase_invoice_header')['supplier'];
            }
            $qb->andWhere("IDENTITY(h.supplier) = :supplierId");
            $qb->setParameter('supplierId', $supplierId);
            
            if (isset($request->request->get('receive_detail_grid')['filter']['material:code']) && isset($request->request->get('receive_detail_grid')['sort']['material:code'])) {
                $add['filter']($qb, 'm', 'code', $request->request->get('receive_detail_grid')['filter']['material:code']);
                $add['sort']($qb, 'm', 'code', $request->request->get('receive_detail_grid')['sort']['material:code']);
            }
            
            if (isset($request->request->get('receive_detail_grid')['filter']['material:name']) && isset($request->request->get('receive_detail_grid')['sort']['material:name'])) {
                $add['filter']($qb, 'm', 'name', $request->request->get('receive_detail_grid')['filter']['material:name']);
                $add['sort']($qb, 'm', 'name', $request->request->get('receive_detail_grid')['sort']['material:name']);
            }
            
            if (isset($request->request->get('receive_detail_grid')['filter']['paper:code']) && isset($request->request->get('receive_detail_grid')['sort']['paper:code'])) {
                $add['filter']($qb, 'p', 'code', $request->request->get('receive_detail_grid')['filter']['paper:code']);
                $add['sort']($qb, 'p', 'code', $request->request->get('receive_detail_grid')['sort']['paper:code']);
            }
            
            if (isset($request->request->get('receive_detail_grid')['filter']['paper:name']) && isset($request->request->get('receive_detail_grid')['sort']['paper:name'])) {
                $add['filter']($qb, 'p', 'name', $request->request->get('receive_detail_grid')['filter']['paper:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('receive_detail_grid')['sort']['paper:name']);
            }
            
            if (isset($request->request->get('receive_detail_grid')['filter']['receiveHeader:supplierDeliveryCodeNumber']) && isset($request->request->get('receive_detail_grid')['sort']['receiveHeader:supplierDeliveryCodeNumber'])) {
                $add['filter']($qb, 'h', 'referenceNumber', $request->request->get('receive_detail_grid')['filter']['receiveHeader:supplierDeliveryCodeNumber']);
                $add['sort']($qb, 'h', 'referenceNumber', $request->request->get('receive_detail_grid')['sort']['receiveHeader:supplierDeliveryCodeNumber']);
            }
            
            $sub = $new(PurchaseInvoiceDetail::class, 'pi');
            $sub->andWhere("IDENTITY(pi.receiveDetail) = {$alias}.id");
            $qb->andWhere($qb->expr()->not($qb->expr()->exists($sub->getDQL())));
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/receive_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'receiveDetails' => $receiveDetails,
        ]);
    }
}
