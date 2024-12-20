<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Sale\SaleOrderDetail;
use App\Form\Stock\StockSaleOrderDetailType;
use App\Grid\Stock\StockSaleOrderDetailGridType;
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

#[Route('/stock/stock_sale_order_detail')]
class StockSaleOrderDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_stock_sale_order_detail__list', methods: ['GET', 'POST'])]
//    #[Security("is_granted('ROLE_SALE_ORDER_ADD') or is_granted('ROLE_SALE_ORDER_EDIT') or is_granted('ROLE_SALE_ORDER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function _list(Request $request, SaleOrderDetailRepository $saleOrderDetailRepository, InventoryRepository $inventoryRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'deliveryDate' => SortDescending::class,
//            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(StockSaleOrderDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $saleOrderDetails) = $saleOrderDetailRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->innerJoin("{$alias}.product", 'p');
            $qb->innerJoin("{$alias}.saleOrderHeader", 'h');
            $qb->innerJoin("h.customer", 'c');
            if (isset($request->request->get('stock_sale_order_detail_grid')['filter']['saleOrderHeader:transactionDate']) && isset($request->request->get('stock_sale_order_detail_grid')['sort']['saleOrderHeader:transactionDate'])) {
                $add['filter']($qb, 'h', 'transactionDate', $request->request->get('stock_sale_order_detail_grid')['filter']['saleOrderHeader:transactionDate']);
                $add['sort']($qb, 'h', 'transactionDate', $request->request->get('stock_sale_order_detail_grid')['sort']['saleOrderHeader:transactionDate']);
            }
            if (isset($request->request->get('stock_sale_order_detail_grid')['filter']['saleOrderHeader:referenceNumber']) && isset($request->request->get('stock_sale_order_detail_grid')['sort']['saleOrderHeader:referenceNumber'])) {
                $add['filter']($qb, 'h', 'referenceNumber', $request->request->get('stock_sale_order_detail_grid')['filter']['saleOrderHeader:referenceNumber']);
                $add['sort']($qb, 'h', 'referenceNumber', $request->request->get('stock_sale_order_detail_grid')['sort']['saleOrderHeader:referenceNumber']);
            }
            if (isset($request->request->get('stock_sale_order_detail_grid')['filter']['customer:company']) && isset($request->request->get('stock_sale_order_detail_grid')['sort']['customer:company'])) {
                $add['filter']($qb, 'c', 'company', $request->request->get('stock_sale_order_detail_grid')['filter']['customer:company']);
                $add['sort']($qb, 'c', 'company', $request->request->get('stock_sale_order_detail_grid')['sort']['customer:company']);
            }
            if (isset($request->request->get('stock_sale_order_detail_grid')['filter']['product:name']) && isset($request->request->get('stock_sale_order_detail_grid')['sort']['product:name'])) {
                $add['filter']($qb, 'p', 'name', $request->request->get('stock_sale_order_detail_grid')['filter']['product:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('stock_sale_order_detail_grid')['sort']['product:name']);
            }
        });

        if ($request->request->has('export')) {
            return $this->export($form, $saleOrderDetails);
        } else {
            return $this->renderForm("stock/stock_sale_order_detail/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'saleOrderDetails' => $saleOrderDetails,
                'stockQuantityList' => $this->getStockQuantityList($saleOrderDetails, $inventoryRepository),
            ]);
        }
    }

    #[Route('/', name: 'app_stock_stock_sale_order_detail_index', methods: ['GET'])]
//    #[Security("is_granted('ROLE_SALE_ORDER_ADD') or is_granted('ROLE_SALE_ORDER_EDIT') or is_granted('ROLE_SALE_ORDER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function index(): Response
    {
        return $this->render("stock/stock_sale_order_detail/index.html.twig");
    }

    #[Route('/{id}', name: 'app_stock_stock_sale_order_detail_show', methods: ['GET'])]
//    #[Security("is_granted('ROLE_SALE_ORDER_ADD') or is_granted('ROLE_SALE_ORDER_EDIT') or is_granted('ROLE_SALE_ORDER_VIEW') or is_granted('ROLE_APPROVAL')")]
    public function show(SaleOrderDetail $saleOrderDetail): Response
    {
        return $this->render('stock/stock_sale_order_detail/show.html.twig', [
            'saleOrderDetail' => $saleOrderDetail,
        ]);
    }

    #[Route('/{id}/edit.html', name: 'app_stock_stock_sale_order_detail_edit', methods: ['GET', 'POST'])]
//    #[IsGranted('ROLE_SALE_ORDER_EDIT')]
    public function edit(Request $request, SaleOrderDetail $saleOrderDetail, SaleOrderDetailRepository $saleOrderDetailRepository): Response
    {
        $form = $this->createForm(StockSaleOrderDetailType::class, $saleOrderDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $saleOrderDetailRepository->add($saleOrderDetail, true);

            return $this->redirectToRoute('app_stock_stock_sale_order_detail_show', ['id' => $saleOrderDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/stock_sale_order_detail/edit.html.twig", [
            'saleOrderDetail' => $saleOrderDetail,
            'form' => $form,
        ]);
    }

    public function export(FormInterface $form, array $saleOrderDetails): Response
    {
        $htmlString = $this->renderView("stock/stock_sale_order_detail/_list_export.html.twig", [
            'form' => $form->createView(),
            'saleOrderDetails' => $saleOrderDetails,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'rencana kirim po.xlsx';
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
