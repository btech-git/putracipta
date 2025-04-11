<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Common\Data\Operator\FilterEqual;
use App\Entity\Master\Paper;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Grid\Report\PaperFscTransactionGridType;
use App\Repository\Master\PaperRepository;
use App\Repository\Production\MasterOrderHeaderRepository;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
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

#[Route('/report/paper_fsc_transaction')]
class PaperFscTransactionController extends AbstractController
{
    #[Route('/_list', name: 'app_report_paper_fsc_transaction__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function _list(Request $request, PaperRepository $paperRepository, PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository, MasterOrderHeaderRepository $masterOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'purchaseOrderPaperHeader:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
            'type' => [FilterEqual::class, Paper::TYPE_FSC]
        ]);
        $form = $this->createForm(PaperFscTransactionGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $papers) = $paperRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->addOrderBy("{$alias}.name", 'ASC');
            
            $qb->andWhere("EXISTS (SELECT d.id FROM " . PurchaseOrderPaperDetail::class . " d JOIN d.purchaseOrderPaperHeader h WHERE {$alias} = d.paper AND h.isCanceled = false AND h.transactionDate BETWEEN :startDate AND :endDate)");
            $qb->andWhere("EXISTS (SELECT m.id FROM " . MasterOrderHeader::class . " m WHERE {$alias} = m.paper AND m.isCanceled = false AND m.transactionDate BETWEEN :startDate AND :endDate)");
            $qb->setParameter('startDate', $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][2]);
            if (!empty($criteria->getFilter()['supplier:company'][1])) {
                $qb->setParameter('supplierCompany', $criteria->getFilter()['supplier:company'][1]);
            }
            if (!empty($criteria->getFilter()['materialSubCategory:code'][1])) {
                $qb->setParameter('materialSubCategoryCode', '%' . $criteria->getFilter()['materialSubCategory:code'][1] . '%');
            }
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
        $purchaseOrderPaperDetails = $this->getPurchaseOrderPaperDetails($purchaseOrderPaperDetailRepository, $criteria, $papers);
        $masterOrderHeaders = $this->getMasterOrderHeaders($masterOrderHeaderRepository, $criteria, $papers);

        if ($request->request->has('export')) {
            return $this->export($form, $papers, $purchaseOrderPaperDetails, $masterOrderHeaders);
        } else {
            return $this->renderForm("report/paper_fsc_transaction/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'papers' => $papers,
                'purchaseOrderPaperDetails' => $purchaseOrderPaperDetails,
                'masterOrderHeaders' => $masterOrderHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_paper_fsc_transaction_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/paper_fsc_transaction/index.html.twig");
    }

    private function getPurchaseOrderPaperDetails(PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository, DataCriteria $criteria, array $papers): array
    {
        $startDate = $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][1];
        $endDate = $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][2];
        $paperPurchaseOrderPaperDetails = $purchaseOrderPaperDetailRepository->findPaperFscPurchaseOrderPaperDetails($papers, $startDate, $endDate);
        $purchaseOrderPaperDetails = [];
        foreach ($paperPurchaseOrderPaperDetails as $paperPurchaseOrderPaperDetail) {
            $purchaseOrderPaperDetails[$paperPurchaseOrderPaperDetail->getPaper()->getId()][] = $paperPurchaseOrderPaperDetail;
        }

        return $purchaseOrderPaperDetails;
    }

    private function getMasterOrderHeaders(MasterOrderHeaderRepository $masterOrderHeaderRepository, DataCriteria $criteria, array $papers): array
    {
        $startDate = $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][1];
        $endDate = $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][2];
        $paperMasterOrderHeaders = $masterOrderHeaderRepository->findPaperFscMasterOrderHeaders($papers, $startDate, $endDate);
        $masterOrderHeaders = [];
        foreach ($paperMasterOrderHeaders as $paperMasterOrderHeader) {
            $masterOrderHeaders[$paperMasterOrderHeader->getPaper()->getId()][] = $paperMasterOrderHeader;
        }

        return $masterOrderHeaders;
    }

    public function export(FormInterface $form, array $papers, array $purchaseOrderPaperDetails, array $masterOrderHeaders): Response
    {
        $htmlString = $this->renderView("report/paper_fsc_transaction/_list_export.html.twig", [
            'form' => $form->createView(),
            'papers' => $papers,
            'purchaseOrderPaperDetails' => $purchaseOrderPaperDetails,
            'masterOrderHeaders' => $masterOrderHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'paper_fsc_transaction.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
