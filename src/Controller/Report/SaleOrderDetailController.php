<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\SaleOrderDetailGridType;
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

#[Route('/report/sale_order_detail')]
class SaleOrderDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_report_sale_order_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function _list(Request $request, SaleOrderDetailRepository $saleOrderDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'saleOrderHeader:orderReceiveDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(SaleOrderDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleOrderDetails) = $saleOrderDetailRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->innerJoin("{$alias}.saleOrderHeader", 'h');
            if (isset($request->request->get('sale_order_detail_grid')['filter']['customer:company']) && isset($request->request->get('sale_order_detail_grid')['sort']['customer:company'])) {
                $qb->innerJoin("h.customer", 'c');
                $add['filter']($qb, 'c', 'company', $request->request->get('sale_order_detail_grid')['filter']['customer:company']);
                $add['sort']($qb, 'c', 'company', $request->request->get('sale_order_detail_grid')['sort']['customer:company']);
            }
            if (isset($request->request->get('sale_order_detail_grid')['filter']['employee:name']) && isset($request->request->get('sale_order_detail_grid')['sort']['employee:name'])) {
                $qb->innerJoin("h.employee", 'm');
                $add['filter']($qb, 'm', 'name', $request->request->get('sale_order_detail_grid')['filter']['employee:name']);
                $add['sort']($qb, 'm', 'name', $request->request->get('sale_order_detail_grid')['sort']['employee:name']);
            }
            $qb->addOrderBy("h.orderReceiveDate", 'ASC');
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        if ($request->request->has('export')) {
            return $this->export($form, $saleOrderDetails);
        } else {
            return $this->renderForm("report/sale_order_detail/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'saleOrderDetails' => $saleOrderDetails,
            ]);
        }
    }

    #[Route('/', name: 'app_report_sale_order_detail_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SALE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/sale_order_detail/index.html.twig");
    }

    public function export(FormInterface $form, array $saleOrderDetails): Response
    {
        $htmlString = $this->renderView("report/sale_order_detail/_list_export.html.twig", [
            'form' => $form->createView(),
            'saleOrderDetails' => $saleOrderDetails,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'sale_order_detail.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
