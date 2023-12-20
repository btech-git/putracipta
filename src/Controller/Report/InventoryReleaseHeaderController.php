<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\InventoryReleaseHeaderGridType;
use App\Repository\Stock\InventoryReleaseHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/inventory_release_header')]
class InventoryReleaseHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_inventory_release_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, InventoryReleaseHeaderRepository $inventoryReleaseHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(InventoryReleaseHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryReleaseHeaders) = $inventoryReleaseHeaderRepository->fetchData($criteria);

        return $this->renderForm("report/inventory_release_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryReleaseHeaders' => $inventoryReleaseHeaders,
        ]);
    }

    #[Route('/', name: 'app_report_inventory_release_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("report/inventory_release_header/index.html.twig");
    }

//    #[Route('/export', name: 'app_report_inventory_release_header_export', methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    public function exportAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $repository = $em->getRepository(InventoryReleaseHeader::class);
//
//        $grid = $this->get('lib.grid.datagrid');
//        $grid->build(InventoryReleaseHeaderGridType::class, $repository, $request);
//
//        $excel = $this->get('phpexcel');
//        $excelXmlReader = $this->get('lib.excel.xml_reader');
//        $xml = $this->renderView('report/inventory_release_header/export.xml.twig', array(
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
