<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Stock\Inventory;
use App\Grid\Report\InventoryStockProductGridType;
use App\Repository\Master\ProductRepository;
use App\Repository\Stock\InventoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/inventory_stock_product')]
class InventoryStockProductController extends AbstractController
{
    #[Route('/_list', name: 'app_report_inventory_stock_product__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_REPORT')]
    public function _list(Request $request, ProductRepository $productRepository, InventoryRepository $inventoryRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'inventory:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(InventoryStockProductGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $products) = $productRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->andWhere("EXISTS (SELECT i.id FROM " . Inventory::class . " i WHERE {$alias} = i.product AND i.isReversed = false AND i.transactionDate BETWEEN :startDate AND :endDate)");
            $qb->setParameter('startDate', $criteria->getFilter()['inventory:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['inventory:transactionDate'][2]);
        });
        $productInventories = $inventoryRepository->findProductInventories($products, $criteria->getFilter()['inventory:transactionDate'][1], $criteria->getFilter()['inventory:transactionDate'][2]);
        $inventories = [];
        foreach ($productInventories as $productInventory) {
            $inventories[$productInventory->getProduct()->getId()][] = $productInventory;
        }

        return $this->renderForm("report/inventory_stock_product/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'products' => $products,
            'inventories' => $inventories,
        ]);
    }

    #[Route('/', name: 'app_report_inventory_stock_product_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_REPORT')]
    public function index(): Response
    {
        return $this->render("report/inventory_stock_product/index.html.twig");
    }
}