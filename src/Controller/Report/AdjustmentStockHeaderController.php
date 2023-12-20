<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\AdjustmentStockHeaderGridType;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/adjustment_stock_header')]
class AdjustmentStockHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_adjustment_stock_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(AdjustmentStockHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $adjustmentStockHeaders) = $adjustmentStockHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('adjustment_stock_header_grid')['filter']['supplier:company']) && isset($request->request->get('adjustment_stock_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('adjustment_stock_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('adjustment_stock_header_grid')['sort']['supplier:company']);
            }
        });

        return $this->renderForm("report/adjustment_stock_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'adjustmentStockHeaders' => $adjustmentStockHeaders,
        ]);
    }

    #[Route('/', name: 'app_report_adjustment_stock_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("report/adjustment_stock_header/index.html.twig");
    }

//    #[Route('/export', name: 'app_report_adjustment_stock_header_export', methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    public function exportAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $repository = $em->getRepository(AdjustmentStockHeader::class);
//
//        $grid = $this->get('lib.grid.datagrid');
//        $grid->build(AdjustmentStockHeaderGridType::class, $repository, $request);
//
//        $excel = $this->get('phpexcel');
//        $excelXmlReader = $this->get('lib.excel.xml_reader');
//        $xml = $this->renderView('report/adjustment_stock_header/export.xml.twig', array(
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
