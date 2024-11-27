<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Sale\SaleOrderDetail;
use App\Grid\Report\SaleOrderHeaderGridType;
use App\Repository\Sale\SaleOrderDetailRepository;
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

#[Route('/report/sale_order_header')]
class SaleOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_sale_order_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function _list(Request $request, SaleOrderHeaderRepository $saleOrderHeaderRepository, SaleOrderDetailRepository $saleOrderDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'orderReceiveDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SaleOrderHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleOrderHeaders) = $saleOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request, $criteria) {
            $qb->addOrderBy("{$alias}.orderReceiveDate", 'ASC');
            $qb->andWhere("{$alias}.isCanceled = false");
            
            $productCodeConditionString = !empty($criteria->getFilter()['product:code'][1]) ? ' AND p.code LIKE :productCode' : '';
            $productNameConditionString = !empty($criteria->getFilter()['product:name'][1]) ? ' AND p.name LIKE :productName' : '';
            $qb->andWhere("EXISTS (SELECT d.id FROM " . SaleOrderDetail::class . " d JOIN d.product p WHERE {$alias} = d.saleOrderHeader AND d.isCanceled = false AND {$alias}.orderReceiveDate BETWEEN :startDate AND :endDate{$productCodeConditionString}{$productNameConditionString})");
            $qb->setParameter('startDate', $criteria->getFilter()['orderReceiveDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['orderReceiveDate'][2]);
            if (!empty($criteria->getFilter()['product:code'][1])) {
                $qb->setParameter('productCode', '%' . $criteria->getFilter()['product:code'][1] . '%');
            }
            if (!empty($criteria->getFilter()['product:name'][1])) {
                $qb->setParameter('productName', '%' . $criteria->getFilter()['product:name'][1] . '%');
            }
        });
        $saleOrderDetails = $this->getSaleOrderDetails($saleOrderDetailRepository, $criteria, $saleOrderHeaders);

        if ($request->request->has('export')) {
            return $this->export($form, $saleOrderHeaders);
        } else {
            return $this->renderForm("report/sale_order_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'saleOrderHeaders' => $saleOrderHeaders,
                'saleOrderDetails' => $saleOrderDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_sale_order_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/sale_order_header/index.html.twig");
    }

    private function getSaleOrderDetails(SaleOrderDetailRepository $saleOrderDetailRepository, DataCriteria $criteria, array $saleOrderHeaders): array
    {
        $startDate = $criteria->getFilter()['orderReceiveDate'][1];
        $endDate = $criteria->getFilter()['orderReceiveDate'][2];
        $productCode = isset($criteria->getFilter()['product:code'][1]) ? $criteria->getFilter()['product:code'][1] : '';
        $productName = isset($criteria->getFilter()['product:name'][1]) ? $criteria->getFilter()['product:name'][1] : '';
        $saleOrderHeaderDetails = $saleOrderDetailRepository->findSaleOrderHeaderDetails($saleOrderHeaders, $startDate, $endDate, $productCode, $productName);
        $saleOrderDetails = [];
        foreach ($saleOrderHeaderDetails as $saleOrderHeaderDetail) {
            $saleOrderDetails[$saleOrderHeaderDetail->getSaleOrderHeader()->getId()][] = $saleOrderHeaderDetail;
        }

        return $saleOrderDetails;
    }

    public function export(FormInterface $form, array $saleOrderHeaders): Response
    {
        $htmlString = $this->renderView("report/sale_order_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'saleOrderHeaders' => $saleOrderHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'sale_order.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
