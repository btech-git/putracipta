<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\SaleOrderHeaderGridType;
use App\Repository\Sale\SaleOrderHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/sale_order_header')]
class SaleOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_sale_order_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SaleOrderHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleOrderHeaders) = $saleOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('sale_order_header_grid')['filter']['customer:company']) && isset($request->request->get('sale_order_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('sale_order_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('sale_order_header_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("report/sale_order_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleOrderHeaders' => $saleOrderHeaders,
        ]);
    }

    #[Route('/', name: 'app_report_sale_order_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("report/sale_order_header/index.html.twig");
    }

//    #[Route('/export', name: 'app_report_sale_order_header_export', methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    public function exportAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $repository = $em->getRepository(SaleOrderHeader::class);
//
//        $grid = $this->get('lib.grid.datagrid');
//        $grid->build(SaleOrderHeaderGridType::class, $repository, $request);
//
//        $excel = $this->get('phpexcel');
//        $excelXmlReader = $this->get('lib.excel.xml_reader');
//        $xml = $this->renderView('report/sale_order_header/export.xml.twig', array(
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
