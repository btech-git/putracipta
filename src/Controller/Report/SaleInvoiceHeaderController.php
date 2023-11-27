<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Report\SaleInvoiceHeaderGridType;
use App\Repository\Sale\SaleInvoiceHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/sale_invoice_header')]
class SaleInvoiceHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_sale_invoice_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(SaleInvoiceHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $saleInvoiceHeaders) = $saleInvoiceHeaderRepository->fetchData($criteria);

        return $this->renderForm("report/sale_invoice_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleInvoiceHeaders' => $saleInvoiceHeaders,
        ]);
    }

    #[Route('/', name: 'app_report_sale_invoice_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("report/sale_invoice_header/index.html.twig");
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
