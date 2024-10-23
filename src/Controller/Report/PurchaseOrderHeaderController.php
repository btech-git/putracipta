<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Grid\Report\PurchaseOrderHeaderGridType;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Purchase\PurchaseOrderHeaderRepository;
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

#[Route('/report/purchase_order_header')]
class PurchaseOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_purchase_order_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function _list(Request $request, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository, PurchaseOrderDetailRepository $purchaseOrderDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(PurchaseOrderHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseOrderHeaders) = $purchaseOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request, $criteria) {
            if (isset($request->request->get('purchase_order_header_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_order_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('purchase_order_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('purchase_order_header_grid')['sort']['supplier:company']);
            }
            $qb->andWhere("{$alias}.isCanceled = false");
            
            $materialCodeConditionString = !empty($criteria->getFilter()['material:code'][1]) ? ' AND p.code LIKE :materialCode' : '';
            $materialNameConditionString = !empty($criteria->getFilter()['material:name'][1]) ? ' AND p.name LIKE :materialName' : '';
            $qb->andWhere("EXISTS (SELECT d.id FROM " . PurchaseOrderDetail::class . " d JOIN d.material p WHERE {$alias} = d.purchaseOrderHeader AND d.isCanceled = false AND {$alias}.transactionDate BETWEEN :startDate AND :endDate{$materialCodeConditionString}{$materialNameConditionString})");
            $qb->setParameter('startDate', $criteria->getFilter()['transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['transactionDate'][2]);
            if (!empty($criteria->getFilter()['material:code'][1])) {
                $qb->setParameter('materialCode', '%' . $criteria->getFilter()['material:code'][1] . '%');
            }
            if (!empty($criteria->getFilter()['material:name'][1])) {
                $qb->setParameter('materialName', '%' . $criteria->getFilter()['material:name'][1] . '%');
            }
        });
        $purchaseOrderDetails = $this->getPurchaseOrderDetails($purchaseOrderDetailRepository, $criteria, $purchaseOrderHeaders);

        if ($request->request->has('export')) {
            return $this->export($form, $purchaseOrderHeaders);
        } else {
            return $this->renderForm("report/purchase_order_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'purchaseOrderHeaders' => $purchaseOrderHeaders,
                'purchaseOrderDetails' => $purchaseOrderDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_purchase_order_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PURCHASE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/purchase_order_header/index.html.twig");
    }

    private function getPurchaseOrderDetails(PurchaseOrderDetailRepository $purchaseOrderDetailRepository, DataCriteria $criteria, array $purchaseOrderHeaders): array
    {
        $startDate = $criteria->getFilter()['transactionDate'][1];
        $endDate = $criteria->getFilter()['transactionDate'][2];
        $materialCode = isset($criteria->getFilter()['material:code'][1]) ? $criteria->getFilter()['material:code'][1] : '';
        $materialName = isset($criteria->getFilter()['material:name'][1]) ? $criteria->getFilter()['material:name'][1] : '';
        $purchaseOrderHeaderDetails = $purchaseOrderDetailRepository->findPurchaseOrderHeaderDetails($purchaseOrderHeaders, $startDate, $endDate, $materialCode, $materialName);
        $purchaseOrderDetails = [];
        foreach ($purchaseOrderHeaderDetails as $purchaseOrderHeaderDetail) {
            $purchaseOrderDetails[$purchaseOrderHeaderDetail->getPurchaseOrderHeader()->getId()][] = $purchaseOrderHeaderDetail;
        }

        return $purchaseOrderDetails;
    }

    public function export(FormInterface $form, array $purchaseOrderHeaders): Response
    {
        $htmlString = $this->renderView("report/purchase_order_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'purchaseOrderHeaders' => $purchaseOrderHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'puchase_order_material.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
