<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\PurchaseInvoiceHeaderGridType;
use App\Repository\Purchase\PurchaseInvoiceHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/purchase_invoice_header')]
class PurchaseInvoiceHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_purchase_invoice_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(PurchaseInvoiceHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseInvoiceHeaders) = $purchaseInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('purchase_invoice_header_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_invoice_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('purchase_invoice_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('purchase_invoice_header_grid')['sort']['supplier:company']);
            }
        });

        return $this->renderForm("report/purchase_invoice_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseInvoiceHeaders' => $purchaseInvoiceHeaders,
        ]);
    }

    #[Route('/', name: 'app_report_purchase_invoice_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("report/purchase_invoice_header/index.html.twig");
    }

//    #[Route('/export', name: 'app_report_purchase_invoice_header_export', methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    public function exportAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $repository = $em->getRepository(PurchaseInvoiceHeader::class);
//
//        $grid = $this->get('lib.grid.datagrid');
//        $grid->build(PurchaseInvoiceHeaderGridType::class, $repository, $request);
//
//        $excel = $this->get('phpexcel');
//        $excelXmlReader = $this->get('lib.excel.xml_reader');
//        $xml = $this->renderView('report/purchase_invoice_header/export.xml.twig', array(
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
