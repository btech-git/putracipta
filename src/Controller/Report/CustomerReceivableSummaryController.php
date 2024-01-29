<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\SaleInvoiceHeaderGridType;
use App\Repository\Sale\SaleInvoiceHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/customer_receivable_summary')]
class CustomerReceivableSummaryController extends AbstractController
{
    #[Route('/_list', name: 'app_report_customer_receivable_summary__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function _list(Request $request, SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SaleInvoiceHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleInvoiceHeaders) = $saleInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.remainingPayment > 0");
        });

        return $this->renderForm("report/customer_receivable_summary/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleInvoiceHeaders' => $saleInvoiceHeaders,
        ]);
    }

    #[Route('/', name: 'app_report_customer_receivable_summary_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/customer_receivable_summary/index.html.twig");
    }

//    #[Route('/export', name: 'app_report_sale_invoice_header_export', methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    public function exportAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $repository = $em->getRepository(SaleInvoiceHeader::class);
//
//        $grid = $this->get('lib.grid.datagrid');
//        $grid->build(SaleInvoiceHeaderGridType::class, $repository, $request);
//
//        $excel = $this->get('phpexcel');
//        $excelXmlReader = $this->get('lib.excel.xml_reader');
//        $xml = $this->renderView('report/sale_invoice_header/export.xml.twig', array(
//            'grid' => $grid->createView(),
//        ));
//        $excelObject = $excelXmlReader->load($xml);
//        $writer = $excel->createWriter($excelObject, 'Excel5');
//        $response = $excel->createStreamedResponse($writer);
//
//        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'report.xls');
//        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
//        $response->headers->set('Pragma', 'public');
//        $response->headers->set('Cache-Control', 'maxage=1');
//        $response->headers->set('Content-Disposition', $dispositionHeader);
//
//        return $response;
//    }
}
