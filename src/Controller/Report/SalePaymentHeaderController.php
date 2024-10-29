<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Sale\SalePaymentDetail;
use App\Grid\Report\SalePaymentHeaderGridType;
use App\Repository\Sale\SalePaymentDetailRepository;
use App\Repository\Sale\SalePaymentHeaderRepository;
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

#[Route('/report/sale_payment_header')]
class SalePaymentHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_sale_payment_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function _list(Request $request, SalePaymentHeaderRepository $salePaymentHeaderRepository, SalePaymentDetailRepository $salePaymentDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SalePaymentHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $salePaymentHeaders) = $salePaymentHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request, $criteria) {
            if (isset($request->request->get('sale_payment_header_grid')['filter']['customer:company']) && isset($request->request->get('sale_payment_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 'c');
                $add['filter']($qb, 'c', 'company', $request->request->get('sale_payment_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 'c', 'company', $request->request->get('sale_payment_header_grid')['sort']['customer:company']);
            }
            $qb->addOrderBy("{$alias}.transactionDate", 'ASC');
            $qb->andWhere("{$alias}.isCanceled = false");
            
            $invoiceOrdinalConditionString = !empty($criteria->getFilter()['saleInvoiceHeader:codeNumberOrdinal'][1]) ? ' AND i.codeNumberOrdinal = :codeNumberOrdinal' : '';
            $invoiceMonthConditionString = !empty($criteria->getFilter()['saleInvoiceHeader:codeNumberMonth'][1]) ? ' AND i.codeNumberMonth = :codeNumberMonth' : '';
            $invoiceYearConditionString = !empty($criteria->getFilter()['saleInvoiceHeader:codeNumberYear'][1]) ? ' AND i.codeNumberYear = :codeNumberYear' : '';
            $qb->andWhere("EXISTS (SELECT d.id FROM " . SalePaymentDetail::class . " d JOIN d.saleInvoiceHeader i WHERE {$alias} = d.salePaymentHeader AND d.isCanceled = false AND {$alias}.transactionDate BETWEEN :startDate AND :endDate{$invoiceOrdinalConditionString}{$invoiceMonthConditionString}{$invoiceYearConditionString})");
            $qb->setParameter('startDate', $criteria->getFilter()['transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['transactionDate'][2]);
            if (!empty($criteria->getFilter()['saleInvoiceHeader:codeNumberOrdinal'][1])) {
                $qb->setParameter('codeNumberOrdinal', $criteria->getFilter()['saleInvoiceHeader:codeNumberOrdinal'][1]);
            }
            if (!empty($criteria->getFilter()['saleInvoiceHeader:codeNumberMonth'][1])) {
                $qb->setParameter('codeNumberMonth', $criteria->getFilter()['saleInvoiceHeader:codeNumberMonth'][1]);
            }
            if (!empty($criteria->getFilter()['saleInvoiceHeader:codeNumberYear'][1])) {
                $qb->setParameter('codeNumberYear', $criteria->getFilter()['saleInvoiceHeader:codeNumberYear'][1]);
            }
        });
        $salePaymentDetails = $this->getSalePaymentDetails($salePaymentDetailRepository, $criteria, $salePaymentHeaders);

        if ($request->request->has('export')) {
            return $this->export($form, $salePaymentHeaders);
        } else {
            return $this->renderForm("report/sale_payment_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'salePaymentHeaders' => $salePaymentHeaders,
                'salePaymentDetails' => $salePaymentDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_sale_payment_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/sale_payment_header/index.html.twig");
    }

    private function getSalePaymentDetails(SalePaymentDetailRepository $salePaymentDetailRepository, DataCriteria $criteria, array $salePaymentHeaders): array
    {
        $startDate = $criteria->getFilter()['transactionDate'][1];
        $endDate = $criteria->getFilter()['transactionDate'][2];
        $invoiceOrdinal = isset($criteria->getFilter()['saleInvoiceHeader:codeNumberOrdinal'][1]) ? $criteria->getFilter()['saleInvoiceHeader:codeNumberOrdinal'][1] : '';
        $invoiceMonth = isset($criteria->getFilter()['saleInvoiceHeader:codeNumberMonth'][1]) ? $criteria->getFilter()['saleInvoiceHeader:codeNumberMonth'][1] : '';
        $invoiceYear = isset($criteria->getFilter()['saleInvoiceHeader:codeNumberYear'][1]) ? $criteria->getFilter()['saleInvoiceHeader:codeNumberYear'][1] : '';
        $salePaymentHeaderDetails = $salePaymentDetailRepository->findSalePaymentHeaderDetails($salePaymentHeaders, $startDate, $endDate, $invoiceOrdinal, $invoiceMonth, $invoiceYear);
        $salePaymentDetails = [];
        foreach ($salePaymentHeaderDetails as $salePaymentHeaderDetail) {
            $salePaymentDetails[$salePaymentHeaderDetail->getSalePaymentHeader()->getId()][] = $salePaymentHeaderDetail;
        }

        return $salePaymentDetails;
    }

    public function export(FormInterface $form, array $salePaymentHeaders): Response
    {
        $htmlString = $this->renderView("report/sale_payment_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'salePaymentHeaders' => $salePaymentHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'sale_payment.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
