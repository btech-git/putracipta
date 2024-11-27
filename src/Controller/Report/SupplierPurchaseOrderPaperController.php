<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Purchase\PurchaseOrderPaperHeader;
use App\Grid\Report\SupplierPurchaseOrderPaperGridType;
use App\Repository\Master\SupplierRepository;
use App\Repository\Purchase\PurchaseOrderPaperHeaderRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/supplier_purchase_order_paper')]
class SupplierPurchaseOrderPaperController extends AbstractController
{
    #[Route('/_list', name: 'app_report_supplier_purchase_order_paper__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function _list(Request $request, SupplierRepository $supplierRepository, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'purchaseOrderPaperHeader:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SupplierPurchaseOrderPaperGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $suppliers) = $supplierRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->addOrderBy("{$alias}.id", 'ASC');
            
            $codeNumberOrdinalConditionString = !empty($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberOrdinal'][1]) ? ' AND s.codeNumberOrdinal = :codeNumberOrdinal' : '';
            $codeNumberMonthConditionString = !empty($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberMonth'][1]) ? ' AND s.codeNumberMonth = :codeNumberMonth' : '';
            $codeNumberYearConditionString = !empty($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberYear'][1]) ? ' AND s.codeNumberYear = :codeNumberYear' : '';
            $qb->andWhere("EXISTS (SELECT s.id FROM " . PurchaseOrderPaperHeader::class . " s WHERE {$alias} = s.supplier AND s.isCanceled = false AND s.transactionDate BETWEEN :startDate AND :endDate{$codeNumberOrdinalConditionString}{$codeNumberMonthConditionString}{$codeNumberYearConditionString})");
            $qb->setParameter('startDate', $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][2]);
            if (!empty($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberOrdinal'][1])) {
                $qb->setParameter('codeNumberOrdinal', $criteria->getFilter()['purchaseOrderPaperHeader:codeNumberOrdinal'][1]);
            }
            if (!empty($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberMonth'][1])) {
                $qb->setParameter('codeNumberMonth', $criteria->getFilter()['purchaseOrderPaperHeader:codeNumberMonth'][1]);
            }
            if (!empty($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberYear'][1])) {
                $qb->setParameter('codeNumberYear', $criteria->getFilter()['purchaseOrderPaperHeader:codeNumberYear'][1]);
            }
        });
        $purchaseOrderPaperHeaders = $this->getPurchaseOrderPaperHeaders($purchaseOrderPaperHeaderRepository, $criteria, $suppliers);

        if ($request->request->has('export')) {
            return $this->export($form, $suppliers, $purchaseOrderPaperHeaders);
        } else {
            return $this->renderForm("report/supplier_purchase_order_paper/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'suppliers' => $suppliers,
                'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_supplier_purchase_order_paper_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/supplier_purchase_order_paper/index.html.twig");
    }

    private function getPurchaseOrderPaperHeaders(PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository, DataCriteria $criteria, array $suppliers): array
    {
        $startDate = $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][1];
        $endDate = $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][2];
        $codeNumberOrdinal = isset($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberOrdinal'][1]) ? $criteria->getFilter()['purchaseOrderPaperHeader:codeNumberOrdinal'][1] : '';
        $codeNumberMonth = isset($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberMonth'][1]) ? $criteria->getFilter()['purchaseOrderPaperHeader:codeNumberMonth'][1] : '';
        $codeNumberYear = isset($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberYear'][1]) ? $criteria->getFilter()['purchaseOrderPaperHeader:codeNumberYear'][1] : '';
        $supplierPurchaseOrderPaperHeaders = $purchaseOrderPaperHeaderRepository->findSupplierPurchaseOrderPaperHeaders($suppliers, $startDate, $endDate, $codeNumberOrdinal, $codeNumberMonth, $codeNumberYear);
        $purchaseOrderPaperHeaders = [];
        foreach ($supplierPurchaseOrderPaperHeaders as $supplierPurchaseOrderPaperHeader) {
            $purchaseOrderPaperHeaders[$supplierPurchaseOrderPaperHeader->getSupplier()->getId()][] = $supplierPurchaseOrderPaperHeader;
        }

        return $purchaseOrderPaperHeaders;
    }

    public function export(FormInterface $form, array $suppliers, array $purchaseOrderPaperHeaders): Response
    {
        $htmlString = $this->renderView("report/supplier_purchase_order_paper/_list_export.html.twig", [
            'form' => $form->createView(),
            'suppliers' => $suppliers,
            'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'purchase_order_paper_per_supplier.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
