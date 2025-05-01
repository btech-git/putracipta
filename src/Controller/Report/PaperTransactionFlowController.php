<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Criteria\DataCriteriaPagination;
use App\Common\Data\Operator\FilterBetween;
use App\Common\Data\Operator\SortAscending;
use App\Grid\Report\PaperTransactionFlowGridType;
use App\Repository\Master\PaperRepository;
use App\Repository\Production\MasterOrderHeaderRepository;
use App\Repository\Purchase\PurchaseOrderPaperHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/paper_transaction_flow')]
class PaperTransactionFlowController extends AbstractController
{
    #[Route('/_list', name: 'app_report_paper_transaction_flow__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function _list(Request $request, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository, MasterOrderHeaderRepository $masterOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $criteria->setSort([
            'transactionDate' => SortAscending::class,
        ]);
        $form = $this->createForm(PaperTransactionFlowGridType::class, $criteria);
        $form->handleRequest($request);

        list($purchaseOrderPaperHeaders, $purchaseOrderPaperHeaderData, $purchaseOrderPaperHeaderCount) = $this->getPurchaseOrderPaperGridData($criteria, $purchaseOrderPaperHeaderRepository);
        list($masterOrderHeaders, $masterOrderHeaderData, $masterOrderHeaderCount) = $this->getMasterOrderGridData($criteria, $masterOrderHeaderRepository);
        
        $count = max($purchaseOrderPaperHeaderCount, $masterOrderHeaderCount);
        $objects = count($purchaseOrderPaperHeaders) >= count($masterOrderHeaders) ? $purchaseOrderPaperHeaders : $masterOrderHeaders;

        if ($request->request->has('export')) {
//            return $this->export($form, $papers, $purchaseOrderPaperDetails);
        } else {
            return $this->renderForm("report/paper_transaction_flow/_list.html.twig", [
                'form' => $form,
                'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
                'purchaseOrderPaperHeaderData' => $purchaseOrderPaperHeaderData,
                'purchaseOrderPaperHeaderCount' => $purchaseOrderPaperHeaderCount,
                'masterOrderHeaders' => $masterOrderHeaders,
                'masterOrderHeaderData' => $masterOrderHeaderData,
                'masterOrderHeaderCount' => $masterOrderHeaderCount,
                'count' => $count,
                'objects' => $objects,
            ]);
        }
    }

    #[Route('/', name: 'app_report_paper_transaction_flow_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/paper_transaction_flow/index.html.twig");
    }
    
    #[Route('/_paper_list', name: 'app_report_paper_transaction_flow__paper_list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function _paperList(Request $request, PaperRepository $paperRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PaperTransactionFlowGridType::class, $criteria);
        $form->handleRequest($request);
        
        $materialSubCategoryId = $criteria->getFilter()['_materialSubCategory:id'][1];
        $paperId = $criteria->getFilter()['_paper:id'][1];
        
        $papers = $paperRepository->findBy(['materialSubCategory' => $materialSubCategoryId], ['name' => 'ASC']);

        return $this->renderForm("report/paper_transaction_flow/_paper_list.html.twig", [
            'form' => $form,
            'paperId' => $paperId,
            'papers' => $papers,
        ]);
    }

//    private function getPurchaseOrderPaperDetails(PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository, DataCriteria $criteria, array $papers): array
//    {
//        $startDate = $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][1];
//        $endDate = $criteria->getFilter()['purchaseOrderPaperHeader:transactionDate'][2];
//        $codeNumberOrdinal = isset($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberOrdinal'][1]) ? $criteria->getFilter()['purchaseOrderPaperHeader:codeNumberOrdinal'][1] : '';
//        $codeNumberMonth = isset($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberMonth'][1]) ? $criteria->getFilter()['purchaseOrderPaperHeader:codeNumberMonth'][1] : '';
//        $codeNumberYear = isset($criteria->getFilter()['purchaseOrderPaperHeader:codeNumberYear'][1]) ? $criteria->getFilter()['purchaseOrderPaperHeader:codeNumberYear'][1] : '';
//        $supplierCompany = isset($criteria->getFilter()['supplier:company'][1]) ? $criteria->getFilter()['supplier:company'][1] : '';
//        $materialSubCategoryCode = isset($criteria->getFilter()['materialSubCategory:code'][1]) ? $criteria->getFilter()['materialSubCategory:code'][1] : '';
//        $paperPurchaseOrderPaperDetails = $purchaseOrderPaperDetailRepository->findPaperPurchaseOrderPaperDetails($papers, $startDate, $endDate, $supplierCompany, $materialSubCategoryCode, $codeNumberOrdinal, $codeNumberMonth, $codeNumberYear);
//        $purchaseOrderPaperDetails = [];
//        foreach ($paperPurchaseOrderPaperDetails as $paperPurchaseOrderPaperDetail) {
//            $purchaseOrderPaperDetails[$paperPurchaseOrderPaperDetail->getPaper()->getId()][] = $paperPurchaseOrderPaperDetail;
//        }
//
//        return $purchaseOrderPaperDetails;
//    }

    private function getPurchaseOrderPaperGridData(DataCriteria $criteria, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): array
    {
        list($purchaseOrderPaperHeaderCount, $purchaseOrderPaperHeaders) = $purchaseOrderPaperHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($criteria) {
            if (!empty($criteria->getFilter()['_paper:id'])) {
                $qb->leftJoin("{$alias}.purchaseOrderPaperDetails", 'd');
                $add['filter']($qb, 'd', 'paper', $criteria->getFilter()['_paper:id']);
            }
        });

        $purchaseOrderPaperHeaderDataCriteria = new DataCriteria();
        $purchaseOrderPaperHeaderDataCriteriaPagination = new DataCriteriaPagination();
        $purchaseOrderPaperHeaderDataCriteriaPagination->setSize(1000);
        $purchaseOrderPaperHeaderDataCriteria->setPagination($purchaseOrderPaperHeaderDataCriteriaPagination);
        $purchaseOrderPaperHeaderItems = $purchaseOrderPaperHeaderRepository->fetchObjects($criteria, function($qb, $alias) use ($purchaseOrderPaperHeaders) {
            $qb->leftJoin("{$alias}.purchaseOrderPaperDetails", 'd');
            $qb->leftJoin("d.receiveDetails", 'dd');
            $qb->leftJoin("dd.receiveHeader", 'dh');
            $qb->leftJoin("dh.purchaseInvoiceHeaders", 'sh');
            $qb->andWhere("{$alias} IN (:purchaseOrderPaperHeaders)")->setParameter('purchaseOrderPaperHeaders', $purchaseOrderPaperHeaders);
        });
        $purchaseOrderPaperHeaderData = [];
        foreach ($purchaseOrderPaperHeaderItems as $purchaseOrderPaperHeaderItem) {
            $purchaseOrderPaperHeaderData[$purchaseOrderPaperHeaderItem->getId()] = $purchaseOrderPaperHeaderItem;
        }

        return [$purchaseOrderPaperHeaders, $purchaseOrderPaperHeaderData, $purchaseOrderPaperHeaderCount];
    }

    private function getMasterOrderGridData(DataCriteria $criteria, MasterOrderHeaderRepository $masterOrderHeaderRepository): array
    {
        list($masterOrderHeaderCount, $masterOrderHeaders) = $masterOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($criteria) {
            if (!empty($criteria->getFilter()['_paper:id'])) {
                $add['filter']($qb, $alias, 'paper', $criteria->getFilter()['_paper:id']);
            }
        });

        $masterOrderHeaderDataCriteria = new DataCriteria();
        $masterOrderHeaderDataCriteriaPagination = new DataCriteriaPagination();
        $masterOrderHeaderDataCriteriaPagination->setSize(1000);
        $masterOrderHeaderDataCriteria->setPagination($masterOrderHeaderDataCriteriaPagination);
        $masterOrderHeaderItems = $masterOrderHeaderRepository->fetchObjects($criteria, function($qb, $alias) use ($masterOrderHeaders) {
            $qb->leftJoin("{$alias}.masterOrderProductDetails", 'md');
            $qb->leftJoin("md.deliveryDetails", 'dd');
            $qb->leftJoin("dd.deliveryHeader", 'dh');
            $qb->leftJoin("dd.saleInvoiceDetails", 'sd');
            $qb->leftJoin("sd.saleInvoiceHeader", 'sh');
            $qb->andWhere("{$alias} IN (:masterOrderHeaders)")->setParameter('masterOrderHeaders', $masterOrderHeaders);
        });
        $masterOrderHeaderData = [];
        foreach ($masterOrderHeaderItems as $masterOrderHeaderItem) {
            $masterOrderHeaderData[$masterOrderHeaderItem->getId()] = $masterOrderHeaderItem;
        }

        return [$masterOrderHeaders, $masterOrderHeaderData, $masterOrderHeaderCount];
    }

//    public function export(FormInterface $form, array $papers, array $purchaseOrderPaperDetails): Response
//    {
//        $htmlString = $this->renderView("report/paper_transaction_flow/_list_export.html.twig", [
//            'form' => $form->createView(),
//            'papers' => $papers,
//            'purchaseOrderPaperDetails' => $purchaseOrderPaperDetails,
//        ]);
//
//        $reader = new Html();
//        $spreadsheet = $reader->loadFromString($htmlString);
//
//        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
//        $response =  new StreamedResponse(function() use ($writer) {
//            $writer->save('php://output');
//        });
//
//        $filename = 'purchase_order_per_paper.xlsx';
//        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
//        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
//        $response->headers->set('Content-Disposition', $dispositionHeader);
//
//        return $response;
//    }
}
