<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Sale\SaleOrderDetailLogData;
use App\Grid\Report\SaleOrderHeaderGridType;
use App\Repository\Sale\SaleOrderDetailLogDataRepository;
use App\Repository\Sale\SaleOrderHeaderRepository;
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

#[Route('/report/sale_order_log')]
class SaleOrderLogController extends AbstractController
{
    #[Route('/_list', name: 'app_report_sale_order_log__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function _list(Request $request, SaleOrderHeaderRepository $saleOrderHeaderRepository, SaleOrderDetailLogDataRepository $saleOrderDetailLogDataRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'orderReceiveDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SaleOrderHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleOrderHeaders) = $saleOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request, $criteria) {
            if (isset($request->request->get('sale_order_log_grid')['filter']['customer:company']) && isset($request->request->get('sale_order_log_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('sale_order_log_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('sale_order_log_grid')['sort']['customer:company']);
            }
            $qb->addOrderBy("{$alias}.orderReceiveDate", 'ASC');
            $qb->andWhere("{$alias}.isCanceled = false");
            
            $productCodeConditionString = !empty($criteria->getFilter()['product:code'][1]) ? ' AND p.code LIKE :productCode' : '';
            $productNameConditionString = !empty($criteria->getFilter()['product:name'][1]) ? ' AND p.name LIKE :productName' : '';
            $qb->andWhere("EXISTS (SELECT d.id FROM " . SaleOrderDetailLogData::class . " d JOIN d.product p WHERE {$alias} = d.saleOrderHeader AND d.isCanceled = false AND {$alias}.orderReceiveDate BETWEEN :startDate AND :endDate{$productCodeConditionString}{$productNameConditionString})");
            $qb->setParameter('startDate', $criteria->getFilter()['orderReceiveDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['orderReceiveDate'][2]);
            if (!empty($criteria->getFilter()['product:code'][1])) {
                $qb->setParameter('productCode', '%' . $criteria->getFilter()['product:code'][1] . '%');
            }
            if (!empty($criteria->getFilter()['product:name'][1])) {
                $qb->setParameter('productName', '%' . $criteria->getFilter()['product:name'][1] . '%');
            }
        });
        $saleOrderDetailLogDatas = $this->getSaleOrderDetailLogDatas($saleOrderDetailLogDataRepository, $criteria, $saleOrderHeaders);

        if ($request->request->has('export')) {
            return $this->export($form, $saleOrderHeaders);
        } else {
            return $this->renderForm("report/sale_order_log/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'saleOrderHeaders' => $saleOrderHeaders,
                'saleOrderDetailLogDatas' => $saleOrderDetailLogDatas,
            ]);
        }
    }

    #[Route('/', name: 'app_report_sale_order_log_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/sale_order_log/index.html.twig");
    }

    private function getSaleOrderDetailLogDatas(SaleOrderDetailLogDataRepository $saleOrderDetailLogDataRepository, DataCriteria $criteria, array $saleOrderHeaders): array
    {
        $startDate = $criteria->getFilter()['orderReceiveDate'][1];
        $endDate = $criteria->getFilter()['orderReceiveDate'][2];
        $productCode = isset($criteria->getFilter()['product:code'][1]) ? $criteria->getFilter()['product:code'][1] : '';
        $productName = isset($criteria->getFilter()['product:name'][1]) ? $criteria->getFilter()['product:name'][1] : '';
        $saleOrderHeaderDetailLogDatas = $saleOrderDetailLogDataRepository->findSaleOrderHeaderDetailLogDatas($saleOrderHeaders, $startDate, $endDate, $productCode, $productName);
        $saleOrderDetailLogDatas = [];
        foreach ($saleOrderHeaderDetailLogDatas as $saleOrderHeaderDetailLogData) {
            $saleOrderDetailLogDatas[$saleOrderHeaderDetailLogData->getSaleOrderHeader()->getId()][] = $saleOrderHeaderDetailLogData;
        }

        return $saleOrderDetailLogDatas;
    }

    public function export(FormInterface $form, array $saleOrderHeaders): Response
    {
        $htmlString = $this->renderView("report/sale_order_log/_list_export.html.twig", [
            'form' => $form->createView(),
            'saleOrderHeaders' => $saleOrderHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'customer_order.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
