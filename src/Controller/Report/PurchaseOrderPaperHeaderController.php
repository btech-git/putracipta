<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Grid\Report\PurchaseOrderPaperHeaderGridType;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
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

#[Route('/report/purchase_order_paper_header')]
class PurchaseOrderPaperHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_purchase_order_paper_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function _list(Request $request, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository, PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(PurchaseOrderPaperHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseOrderPaperHeaders) = $purchaseOrderPaperHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request, $criteria) {
            if (isset($request->request->get('purchase_order_paper_header_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_order_paper_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('purchase_order_paper_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('purchase_order_paper_header_grid')['sort']['supplier:company']);
            }
            $qb->andWhere("{$alias}.isCanceled = false");
            
            $paperCodeConditionString = !empty($criteria->getFilter()['paper:code'][1]) ? ' AND p.code = :paperCode' : '';
            $paperNameConditionString = !empty($criteria->getFilter()['paper:name'][1]) ? ' AND p.name LIKE :paperName' : '';
            $paperTypeConditionString = !empty($criteria->getFilter()['paper:type'][1]) ? ' AND p.type = :paperType' : '';
            $paperWeightConditionString = !empty($criteria->getFilter()['paper:weight'][1]) ? ' AND p.weight = :paperWeight' : '';
            $materialSubCategoryCodeConditionString = !empty($criteria->getFilter()['materialSubCategory:code'][1]) ? ' AND c.code LIKE :materialSubCategoryCode' : '';
            $qb->andWhere("EXISTS (SELECT d.id FROM " . PurchaseOrderPaperDetail::class . " d JOIN d.paper p JOIN p.materialSubCategory c WHERE {$alias} = d.purchaseOrderPaperHeader AND d.isCanceled = false AND {$alias}.transactionDate BETWEEN :startDate AND :endDate{$paperCodeConditionString}{$paperNameConditionString}{$paperTypeConditionString}{$paperWeightConditionString}{$materialSubCategoryCodeConditionString})");
            $qb->setParameter('startDate', $criteria->getFilter()['transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['transactionDate'][2]);
            if (!empty($criteria->getFilter()['paper:code'][1])) {
                $qb->setParameter('paperCode', $criteria->getFilter()['paper:code'][1]);
            }
            if (!empty($criteria->getFilter()['paper:name'][1])) {
                $qb->setParameter('paperName', '%' . $criteria->getFilter()['paper:name'][1] . '%');
            }
            if (!empty($criteria->getFilter()['paper:type'][1])) {
                $qb->setParameter('paperType', $criteria->getFilter()['paper:type'][1]);
            }
            if (!empty($criteria->getFilter()['paper:weight'][1])) {
                $qb->setParameter('paperWeight', $criteria->getFilter()['paper:weight'][1]);
            }
            if (!empty($criteria->getFilter()['materialSubCategory:code'][1])) {
                $qb->setParameter('materialSubCategoryCode', '%' . $criteria->getFilter()['materialSubCategory:code'][1] . '%');
            }
        });
        $purchaseOrderPaperDetails = $this->getPurchaseOrderPaperDetails($purchaseOrderPaperDetailRepository, $criteria, $purchaseOrderPaperHeaders);

        if ($request->request->has('export')) {
            return $this->export($form, $purchaseOrderPaperHeaders);
        } else {
            return $this->renderForm("report/purchase_order_paper_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
                'purchaseOrderPaperDetails' => $purchaseOrderPaperDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_purchase_order_paper_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/purchase_order_paper_header/index.html.twig");
    }

    private function getPurchaseOrderPaperDetails(PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository, DataCriteria $criteria, array $purchaseOrderPaperHeaders): array
    {
        $startDate = $criteria->getFilter()['transactionDate'][1];
        $endDate = $criteria->getFilter()['transactionDate'][2];
        $paperCode = isset($criteria->getFilter()['paper:code'][1]) ? $criteria->getFilter()['paper:code'][1] : '';
        $paperName = isset($criteria->getFilter()['paper:name'][1]) ? $criteria->getFilter()['paper:name'][1] : '';
        $paperType = isset($criteria->getFilter()['paper:type'][1]) ? $criteria->getFilter()['paper:type'][1] : '';
        $paperWeight = isset($criteria->getFilter()['paper:weight'][1]) ? $criteria->getFilter()['paper:weight'][1] : '';
        $materialSubCategoryCode = isset($criteria->getFilter()['materialSubCategory:code'][1]) ? $criteria->getFilter()['materialSubCategory:code'][1] : '';
        $purchaseOrderPaperHeaderDetails = $purchaseOrderPaperDetailRepository->findPurchaseOrderPaperHeaderDetails($purchaseOrderPaperHeaders, $startDate, $endDate, $paperCode, $paperName, $paperType, $paperWeight, $materialSubCategoryCode);
        $purchaseOrderPaperDetails = [];
        foreach ($purchaseOrderPaperHeaderDetails as $purchaseOrderPaperHeaderDetail) {
            $purchaseOrderPaperDetails[$purchaseOrderPaperHeaderDetail->getPurchaseOrderPaperHeader()->getId()][] = $purchaseOrderPaperHeaderDetail;
        }

        return $purchaseOrderPaperDetails;
    }

    public function export(FormInterface $form, array $purchaseOrderPaperHeaders): Response
    {
        $htmlString = $this->renderView("report/purchase_order_paper_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'puchase_order_paper.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
