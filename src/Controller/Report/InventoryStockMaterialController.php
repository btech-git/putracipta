<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Stock\Inventory;
use App\Grid\Report\InventoryStockMaterialGridType;
use App\Repository\Master\MaterialRepository;
use App\Repository\Stock\InventoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/inventory_stock_material')]
class InventoryStockMaterialController extends AbstractController
{
    #[Route('/_list', name: 'app_report_inventory_stock_material__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_REPORT')]
    public function _list(Request $request, MaterialRepository $materialRepository, InventoryRepository $inventoryRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'inventory:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(InventoryStockMaterialGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $materials) = $materialRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $warehouseConditionString = !empty($criteria->getFilter()['inventory:warehouse'][1]) ? 'AND IDENTITY(i.warehouse) = :warehouseId' : '';
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->andWhere("EXISTS (SELECT i.id FROM " . Inventory::class . " i WHERE {$alias} = i.material AND i.isReversed = false AND i.transactionDate BETWEEN :startDate AND :endDate {$warehouseConditionString})");
            $qb->setParameter('startDate', $criteria->getFilter()['inventory:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['inventory:transactionDate'][2]);
            if (!empty($criteria->getFilter()['inventory:warehouse'][1])) {
                $qb->setParameter('warehouseId', $criteria->getFilter()['inventory:warehouse'][1]);
            }
            $qb->addOrderBy("{$alias}.id", 'ASC');
        });
        $beginningStockList = $this->getBeginningStockList($inventoryRepository, $criteria, $materials);
        $inventories = $this->getInventories($inventoryRepository, $criteria, $materials);

        return $this->renderForm("report/inventory_stock_material/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'materials' => $materials,
            'beginningStockList' => $beginningStockList,
            'inventories' => $inventories,
        ]);
    }

    #[Route('/', name: 'app_report_inventory_stock_material_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_REPORT')]
    public function index(): Response
    {
        return $this->render("report/inventory_stock_material/index.html.twig");
    }

    private function getBeginningStockList(InventoryRepository $inventoryRepository, DataCriteria $criteria, array $materials): array
    {
        $warehouseId = isset($criteria->getFilter()['inventory:warehouse'][1]) ? $criteria->getFilter()['inventory:warehouse'][1] : '';
        $startDate = $criteria->getFilter()['inventory:transactionDate'][1];
        $materialBeginningStockList = $inventoryRepository->getMaterialBeginningStockList($materials, $startDate, $warehouseId);
        $beginningStockList = [];
        foreach ($materialBeginningStockList as $materialBeginningStockItem) {
            $beginningStockList[$materialBeginningStockItem['materialId']] = $materialBeginningStockItem['beginningStock'];
        }

        return $beginningStockList;
    }

    private function getInventories(InventoryRepository $inventoryRepository, DataCriteria $criteria, array $materials): array
    {
        $warehouseId = isset($criteria->getFilter()['inventory:warehouse'][1]) ? $criteria->getFilter()['inventory:warehouse'][1] : '';
        $startDate = $criteria->getFilter()['inventory:transactionDate'][1];
        $endDate = $criteria->getFilter()['inventory:transactionDate'][2];
        $materialInventories = $inventoryRepository->findMaterialInventories($materials, $startDate, $endDate, $warehouseId);
        $inventories = [];
        foreach ($materialInventories as $materialInventory) {
            $inventories[$materialInventory->getMaterial()->getId()][] = $materialInventory;
        }

        return $inventories;
    }
}