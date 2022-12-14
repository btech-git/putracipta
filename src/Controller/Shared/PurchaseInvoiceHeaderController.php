<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\PurchaseInvoiceHeaderGridType;
use App\Repository\Transaction\PurchaseInvoiceHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/purchase_invoice_header')]
class PurchaseInvoiceHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_purchase_invoice_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseInvoiceHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseInvoiceHeaders) = $purchaseInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias) use ($request) {
            $supplierId = '';
            if (isset($request->query->get('purchase_payment_header')['supplier'])) {
                $supplierId = $request->query->get('purchase_payment_header')['supplier'];
            }
            if (!empty($supplierId)) {
                $qb->andWhere("IDENTITY({$alias}.supplier) = :supplierId");
                $qb->setParameter('supplierId', $supplierId);
            }
            $qb->andWhere("{$alias}.totalRemaining > 0.00");
        });

        return $this->renderForm("shared/purchase_invoice_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseInvoiceHeaders' => $purchaseInvoiceHeaders,
        ]);
    }
}
