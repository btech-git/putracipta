<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Purchase\PurchaseInvoiceHeader;
use App\Entity\Purchase\PurchaseReturnHeader;
use App\Grid\Shared\ReceiveHeaderGridType;
use App\Repository\Purchase\ReceiveHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/receive_header')]
class ReceiveHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_receive_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ReceiveHeaderRepository $receiveHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ReceiveHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $receiveHeaders) = $receiveHeaderRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            if ($request->query->has('purchase_return_header')) {
                $sub = $new(PurchaseReturnHeader::class, 'p');
                $sub->andWhere("IDENTITY(p.receiveHeader) = {$alias}.id");
                $qb->leftJoin("{$alias}.purchaseReturnHeaders", 'r');
                $qb->andWhere($qb->expr()->orX('r.isCanceled = true', $qb->expr()->not($qb->expr()->exists($sub->getDQL()))));
            } else if ($request->query->has('purchase_invoice_header')) {
                $sub = $new(PurchaseInvoiceHeader::class, 'p');
                $sub->andWhere("IDENTITY(p.receiveHeader) = {$alias}.id");
                $qb->leftJoin("{$alias}.purchaseInvoiceHeaders", 'i');
                $qb->andWhere($qb->expr()->orX('i.isCanceled = true', $qb->expr()->not($qb->expr()->exists($sub->getDQL()))));
            }
            
            if (isset($request->query->get('receive_header_grid')['filter']['supplier:company']) && isset($request->query->get('receive_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->query->get('receive_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->query->get('receive_header_grid')['sort']['supplier:company']);
            }
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/receive_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'receiveHeaders' => $receiveHeaders,
        ]);
    }
}
