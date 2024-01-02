<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\SaleOrderHeaderGridType;
use App\Repository\Sale\SaleOrderHeaderRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/sale_order_header')]
class SaleOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_sale_order_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        list($form, $count, $saleOrderHeaders) = $this->getReportData($request, $saleOrderHeaderRepository);

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

    #[Route('/export', name: 'app_report_sale_order_header_export', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function export(Request $request, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        list(, , $saleOrderHeaders) = $this->getReportData($request, $saleOrderHeaderRepository);
        $htmlString = $this->renderView("report/sale_order_header/export.html.twig", [
            'saleOrderHeaders' => $saleOrderHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'report.xlsx');
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }

    public function getReportData(Request $request, SaleOrderHeaderRepository $saleOrderHeaderRepository): array
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

        return [$form, $count, $saleOrderHeaders];
    }
}
