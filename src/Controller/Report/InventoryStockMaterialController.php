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
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->andWhere("EXISTS (SELECT i.id FROM " . Inventory::class . " i WHERE {$alias} = i.material AND i.isReversed = false AND i.transactionDate BETWEEN :startDate AND :endDate)");
            $qb->setParameter('startDate', $criteria->getFilter()['inventory:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['inventory:transactionDate'][2]);
        });
        $materialInventories = $inventoryRepository->findMaterialInventories($materials, $criteria->getFilter()['inventory:transactionDate'][1], $criteria->getFilter()['inventory:transactionDate'][2]);
        $inventories = [];
        foreach ($materialInventories as $materialInventory) {
            $inventories[$materialInventory->getMaterial()->getId()][] = $materialInventory;
        }

        return $this->renderForm("report/inventory_stock_material/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'materials' => $materials,
            'inventories' => $inventories,
        ]);
    }

    #[Route('/', name: 'app_report_inventory_stock_material_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_REPORT')]
    public function index(): Response
    {
        return $this->render("report/inventory_stock_material/index.html.twig");
    }
}