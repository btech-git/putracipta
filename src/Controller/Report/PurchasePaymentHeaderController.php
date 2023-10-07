<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Report\PurchasePaymentHeaderGridType;
use App\Repository\Purchase\PurchasePaymentHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/purchase_payment_header')]
class PurchasePaymentHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_purchase_payment_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchasePaymentHeaderRepository $purchasePaymentHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchasePaymentHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchasePaymentHeaders) = $purchasePaymentHeaderRepository->fetchData($criteria);

        return $this->renderForm("report/purchase_payment_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchasePaymentHeaders' => $purchasePaymentHeaders,
        ]);
    }

    #[Route('/', name: 'app_report_purchase_payment_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("report/purchase_payment_header/index.html.twig");
    }

//    #[Route('/export', name: 'app_report_purchase_payment_header_export', methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    public function exportAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $repository = $em->getRepository(PurchasePaymentHeader::class);
//
//        $grid = $this->get('lib.grid.datagrid');
//        $grid->build(PurchasePaymentHeaderGridType::class, $repository, $request);
//
//        $excel = $this->get('phpexcel');
//        $excelXmlReader = $this->get('lib.excel.xml_reader');
//        $xml = $this->renderView('report/purchase_payment_header/export.xml.twig', array(
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
