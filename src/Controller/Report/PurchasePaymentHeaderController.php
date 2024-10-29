<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Purchase\PurchasePaymentDetail;
use App\Grid\Report\PurchasePaymentHeaderGridType;
use App\Repository\Purchase\PurchasePaymentDetailRepository;
use App\Repository\Purchase\PurchasePaymentHeaderRepository;
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

#[Route('/report/purchase_payment_header')]
class PurchasePaymentHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_purchase_payment_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function _list(Request $request, PurchasePaymentHeaderRepository $purchasePaymentHeaderRepository, PurchasePaymentDetailRepository $purchasePaymentDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(PurchasePaymentHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchasePaymentHeaders) = $purchasePaymentHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request, $criteria) {
            if (isset($request->request->get('purchase_payment_header_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_payment_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('purchase_payment_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('purchase_payment_header_grid')['sort']['supplier:company']);
            }
            $qb->andWhere("{$alias}.isCanceled = false");
            
            $supplierInvoiceConditionString = !empty($criteria->getFilter()['purchaseInvoiceHeader:supplierInvoiceCodeNumber'][1]) ? ' AND i.supplierInvoiceCodeNumber LIKE :supplierInvoiceCodeNumber' : '';
            $qb->andWhere("EXISTS (SELECT d.id FROM " . PurchasePaymentDetail::class . " d JOIN d.purchaseInvoiceHeader i WHERE {$alias} = d.purchasePaymentHeader AND d.isCanceled = false AND {$alias}.transactionDate BETWEEN :startDate AND :endDate{$supplierInvoiceConditionString})");
            $qb->setParameter('startDate', $criteria->getFilter()['transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['transactionDate'][2]);
            if (!empty($criteria->getFilter()['purchaseInvoiceHeader:supplierInvoiceCodeNumber'][1])) {
                $qb->setParameter('supplierInvoiceCodeNumber', '%' . $criteria->getFilter()['purchaseInvoiceHeader:supplierInvoiceCodeNumber'][1] . '%');
            }
        });
        $purchasePaymentDetails = $this->getPurchasePaymentDetails($purchasePaymentDetailRepository, $criteria, $purchasePaymentHeaders);

        if ($request->request->has('export')) {
            return $this->export($form, $purchasePaymentHeaders);
        } else {
            return $this->renderForm("report/purchase_payment_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'purchasePaymentHeaders' => $purchasePaymentHeaders,
                'purchasePaymentDetails' => $purchasePaymentDetails, 
            ]);
        }
    }

    #[Route('/', name: 'app_report_purchase_payment_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/purchase_payment_header/index.html.twig");
    }

    private function getPurchasePaymentDetails(PurchasePaymentDetailRepository $purchasePaymentDetailRepository, DataCriteria $criteria, array $purchasePaymentHeaders): array
    {
        $startDate = $criteria->getFilter()['transactionDate'][1];
        $endDate = $criteria->getFilter()['transactionDate'][2];
        $supplierInvoiceCodeNumber = isset($criteria->getFilter()['purchaseInvoiceHeader:supplierInvoiceCodeNumber'][1]) ? $criteria->getFilter()['purchaseInvoiceHeader:supplierInvoiceCodeNumber'][1] : '';
        $purchasePaymentHeaderDetails = $purchasePaymentDetailRepository->findPurchasePaymentHeaderDetails($purchasePaymentHeaders, $startDate, $endDate, $supplierInvoiceCodeNumber);
        $purchasePaymentDetails = [];
        foreach ($purchasePaymentHeaderDetails as $purchasePaymentHeaderDetail) {
            $purchasePaymentDetails[$purchasePaymentHeaderDetail->getPurchasePaymentHeader()->getId()][] = $purchasePaymentHeaderDetail;
        }

        return $purchasePaymentDetails;
    }

    public function export(FormInterface $form, array $purchasePaymentHeaders): Response
    {
        $htmlString = $this->renderView("report/purchase_payment_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'purchasePaymentHeaders' => $purchasePaymentHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'purchase_payment.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
