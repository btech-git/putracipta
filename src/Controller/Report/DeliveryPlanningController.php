<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Grid\Report\DeliveryPlanningGridType;
use App\Repository\Sale\SaleOrderDetailRepository;
use App\Repository\Stock\InventoryRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/delivery_planning')]
class DeliveryPlanningController extends AbstractController
{
    #[Route('/_list', name: 'app_report_delivery_planning__list', methods: ['GET', 'POST'])]
//    #[Security("is_granted('ROLE_SALE_ORDER_ADD') or is_granted('ROLE_SALE_ORDER_EDIT') or is_granted('ROLE_SALE_ORDER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _list(Request $request, SaleOrderDetailRepository $saleOrderDetailRepository, InventoryRepository $inventoryRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'deliveryDate' => SortDescending::class,
//            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(DeliveryPlanningGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleOrderDetails) = $saleOrderDetailRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->innerJoin("{$alias}.product", 'p');
            $qb->innerJoin("{$alias}.saleOrderHeader", 'h');
            if (isset($request->request->get('delivery_planning_grid')['filter']['saleOrderHeader:transactionDate']) && isset($request->request->get('delivery_planning_grid')['sort']['saleOrderHeader:transactionDate'])) {
                $add['filter']($qb, 'h', 'transactionDate', $request->request->get('delivery_planning_grid')['filter']['saleOrderHeader:transactionDate']);
                $add['sort']($qb, 'h', 'transactionDate', $request->request->get('delivery_planning_grid')['sort']['saleOrderHeader:transactionDate']);
            }
            if (isset($request->request->get('delivery_planning_grid')['filter']['saleOrderHeader:referenceNumber']) && isset($request->request->get('delivery_planning_grid')['sort']['saleOrderHeader:referenceNumber'])) {
                $add['filter']($qb, 'h', 'referenceNumber', $request->request->get('delivery_planning_grid')['filter']['saleOrderHeader:referenceNumber']);
                $add['sort']($qb, 'h', 'referenceNumber', $request->request->get('delivery_planning_grid')['sort']['saleOrderHeader:referenceNumber']);
            }
            if (isset($request->request->get('delivery_planning_grid')['filter']['saleOrderHeader:customer']) && isset($request->request->get('delivery_planning_grid')['sort']['saleOrderHeader:customer'])) {
                $add['filter']($qb, 'h', 'customer', $request->request->get('delivery_planning_grid')['filter']['saleOrderHeader:customer']);
                $add['sort']($qb, 'h', 'customer', $request->request->get('delivery_planning_grid')['sort']['saleOrderHeader:customer']);
            }
            if (isset($request->request->get('delivery_planning_grid')['filter']['product:name']) && isset($request->request->get('delivery_planning_grid')['sort']['product:name'])) {
                $add['filter']($qb, 'p', 'name', $request->request->get('delivery_planning_grid')['filter']['product:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('delivery_planning_grid')['sort']['product:name']);
            }
        });

        if ($request->request->has('export')) {
            return $this->export($form, $saleOrderDetails);
        } else {
            return $this->renderForm("report/delivery_planning/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'saleOrderDetails' => $saleOrderDetails,
                'stockQuantityList' => $this->getStockQuantityList($saleOrderDetails, $inventoryRepository),
            ]);
        }
    }

    #[Route('/', name: 'app_report_delivery_planning_index', methods: ['GET'])]
//    #[Security("is_granted('ROLE_SALE_ORDER_ADD') or is_granted('ROLE_SALE_ORDER_EDIT') or is_granted('ROLE_SALE_ORDER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function index(): Response
    {
        return $this->render("report/delivery_planning/index.html.twig");
    }

    public function export(FormInterface $form, array $saleOrderDetails): Response
    {
        $htmlString = $this->renderView("report/delivery_planning/_list_export.html.twig", [
            'form' => $form->createView(),
            'saleOrderDetails' => $saleOrderDetails,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'rencana_kirim_po.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
    
    public function getStockQuantityList(array $saleOrderDetails, InventoryRepository $inventoryRepository): array
    {
        $products = array_map(fn($saleOrderDetail) => $saleOrderDetail->getProduct(), $saleOrderDetails);
        $stockQuantityList = $inventoryRepository->getAllWarehouseProductStockQuantityList($products);
        $stockQuantityListIndexed = array_column($stockQuantityList, 'stockQuantity', 'productId');
        
        return $stockQuantityListIndexed;
    }
}
