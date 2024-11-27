<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Sale\SaleReturnDetail;
use App\Grid\Report\SaleReturnHeaderGridType;
use App\Repository\Sale\SaleReturnDetailRepository;
use App\Repository\Sale\SaleReturnHeaderRepository;
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

#[Route('/report/sale_return_header')]
class SaleReturnHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_sale_return_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function _list(Request $request, SaleReturnHeaderRepository $saleReturnHeaderRepository, SaleReturnDetailRepository $saleReturnDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SaleReturnHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleReturnHeaders) = $saleReturnHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request, $criteria) {
            $qb->addOrderBy("{$alias}.transactionDate", 'ASC');
            $qb->andWhere("{$alias}.isCanceled = false");
            
            $productCodeConditionString = !empty($criteria->getFilter()['product:code'][1]) ? ' AND p.code LIKE :productCode' : '';
            $productNameConditionString = !empty($criteria->getFilter()['product:name'][1]) ? ' AND p.name LIKE :productName' : '';
            $qb->andWhere("EXISTS (SELECT d.id FROM " . SaleReturnDetail::class . " d JOIN d.product p WHERE {$alias} = d.saleReturnHeader AND d.isCanceled = false AND {$alias}.transactionDate BETWEEN :startDate AND :endDate{$productCodeConditionString}{$productNameConditionString})");
            $qb->setParameter('startDate', $criteria->getFilter()['transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['transactionDate'][2]);
            if (!empty($criteria->getFilter()['product:code'][1])) {
                $qb->setParameter('productCode', '%' . $criteria->getFilter()['product:code'][1] . '%');
            }
            if (!empty($criteria->getFilter()['product:name'][1])) {
                $qb->setParameter('productName', '%' . $criteria->getFilter()['product:name'][1] . '%');
            }
        });
        $saleReturnDetails = $this->getSaleReturnDetails($saleReturnDetailRepository, $criteria, $saleReturnHeaders);

        if ($request->request->has('export')) {
            return $this->export($form, $saleReturnHeaders);
        } else {
            return $this->renderForm("report/sale_return_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'saleReturnHeaders' => $saleReturnHeaders,
                'saleReturnDetails' => $saleReturnDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_sale_return_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_FINISHED_GOODS_REPORT')]
    public function index(): Response
    {
        return $this->render("report/sale_return_header/index.html.twig");
    }

    private function getSaleReturnDetails(SaleReturnDetailRepository $saleReturnDetailRepository, DataCriteria $criteria, array $saleReturnHeaders): array
    {
        $startDate = $criteria->getFilter()['transactionDate'][1];
        $endDate = $criteria->getFilter()['transactionDate'][2];
        $productCode = isset($criteria->getFilter()['product:code'][1]) ? $criteria->getFilter()['product:code'][1] : '';
        $productName = isset($criteria->getFilter()['product:name'][1]) ? $criteria->getFilter()['product:name'][1] : '';
        $saleReturnHeaderDetails = $saleReturnDetailRepository->findSaleReturnHeaderDetails($saleReturnHeaders, $startDate, $endDate, $productCode, $productName);
        $saleReturnDetails = [];
        foreach ($saleReturnHeaderDetails as $saleReturnHeaderDetail) {
            $saleReturnDetails[$saleReturnHeaderDetail->getSaleReturnHeader()->getId()][] = $saleReturnHeaderDetail;
        }

        return $saleReturnDetails;
    }

    public function export(FormInterface $form, array $saleReturnHeaders): Response
    {
        $htmlString = $this->renderView("report/sale_return_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'saleReturnHeaders' => $saleReturnHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'sale_return.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
