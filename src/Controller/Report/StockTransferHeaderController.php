<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\StockTransferHeaderGridType;
use App\Repository\Stock\StockTransferHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/stock_transfer_header')]
class StockTransferHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_stock_transfer_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_REPORT')]
    public function _list(Request $request, StockTransferHeaderRepository $stockTransferHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(StockTransferHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $stockTransferHeaders) = $stockTransferHeaderRepository->fetchData($criteria);

        return $this->renderForm("report/stock_transfer_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'stockTransferHeaders' => $stockTransferHeaders,
        ]);
    }

    #[Route('/', name: 'app_report_stock_transfer_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_REPORT')]
    public function index(): Response
    {
        return $this->render("report/stock_transfer_header/index.html.twig");
    }

//    #[Route('/export', name: 'app_report_stock_transfer_header_export', methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    public function exportAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $repository = $em->getRepository(StockTransferHeader::class);
//
//        $grid = $this->get('lib.grid.datagrid');
//        $grid->build(StockTransferHeaderGridType::class, $repository, $request);
//
//        $excel = $this->get('phpexcel');
//        $excelXmlReader = $this->get('lib.excel.xml_reader');
//        $xml = $this->renderView('report/stock_transfer_header/export.xml.twig', array(
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
