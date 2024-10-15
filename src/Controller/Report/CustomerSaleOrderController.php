<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Sale\SaleOrderDetail;
use App\Grid\Report\CustomerSaleOrderGridType;
use App\Repository\Master\CustomerRepository;
use App\Repository\Sale\SaleOrderDetailRepository;
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

#[Route('/report/customer_sale_order')]
class CustomerSaleOrderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_customer_sale_order__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function _list(Request $request, CustomerRepository $customerRepository, SaleOrderDetailRepository $saleOrderDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'saleOrderHeader:orderReceiveDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(CustomerSaleOrderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $customers) = $customerRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->addOrderBy("{$alias}.id", 'ASC');
            $productCodeConditionString = !empty($criteria->getFilter()['product:code'][1]) ? ' AND p.code LIKE :productCode' : '';
            $productNameConditionString = !empty($criteria->getFilter()['product:name'][1]) ? ' AND p.name LIKE :productName' : '';
            $qb->andWhere("EXISTS (SELECT s.id FROM " . SaleOrderDetail::class . " d JOIN d.saleOrderHeader s JOIN d.product p WHERE {$alias} = s.customer AND s.isCanceled = false AND s.orderReceiveDate BETWEEN :startDate AND :endDate{$productCodeConditionString}{$productNameConditionString})");
            $qb->setParameter('startDate', $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][2]);
            if (!empty($criteria->getFilter()['product:code'][1])) {
                $qb->setParameter('productCode', '%' . $criteria->getFilter()['product:code'][1] . '%');
            }
            if (!empty($criteria->getFilter()['product:name'][1])) {
                $qb->setParameter('productName', '%' . $criteria->getFilter()['product:name'][1] . '%');
            }
        });
        $saleOrderDetails = $this->getSaleOrderDetails($saleOrderDetailRepository, $criteria, $customers);

        if ($request->request->has('export')) {
            return $this->export($form, $customers, $saleOrderDetails);
        } else {
            return $this->renderForm("report/customer_sale_order/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'customers' => $customers,
                'saleOrderDetails' => $saleOrderDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_customer_sale_order_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/customer_sale_order/index.html.twig");
    }

    private function getSaleOrderDetails(SaleOrderDetailRepository $saleOrderDetailRepository, DataCriteria $criteria, array $customers): array
    {
        $startDate = $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][1];
        $endDate = $criteria->getFilter()['saleOrderHeader:orderReceiveDate'][2];
        $productCode = isset($criteria->getFilter()['product:code'][1]) ? $criteria->getFilter()['product:code'][1] : '';
        $productName = isset($criteria->getFilter()['product:name'][1]) ? $criteria->getFilter()['product:name'][1] : '';
        $customerSaleOrderDetails = $saleOrderDetailRepository->findCustomerSaleOrderDetails($customers, $startDate, $endDate, $productCode, $productName);
        $saleOrderDetails = [];
        foreach ($customerSaleOrderDetails as $customerSaleOrderDetail) {
            $saleOrderDetails[$customerSaleOrderDetail->getSaleOrderHeader()->getCustomer()->getId()][] = $customerSaleOrderDetail;
        }

        return $saleOrderDetails;
    }

    public function export(FormInterface $form, array $customers, array $saleOrderHeaders): Response
    {
        $htmlString = $this->renderView("report/customer_sale_order/_list_export.html.twig", [
            'form' => $form->createView(),
            'customers' => $customers,
            'saleOrderHeaders' => $saleOrderHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'sale_order_per_customer.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
