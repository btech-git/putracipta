<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Stock\Inventory;
use App\Grid\Report\InventoryStockPaperGridType;
use App\Repository\Master\PaperRepository;
use App\Repository\Stock\InventoryRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/inventory_stock_paper')]
class InventoryStockPaperController extends AbstractController
{
    #[Route('/_list', name: 'app_report_inventory_stock_paper__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_REPORT')]
    public function _list(Request $request, PaperRepository $paperRepository, InventoryRepository $inventoryRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'inventory:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(InventoryStockPaperGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $papers) = $paperRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->andWhere("EXISTS (SELECT i.id FROM " . Inventory::class . " i WHERE {$alias} = i.paper AND i.isReversed = false AND i.transactionDate BETWEEN :startDate AND :endDate)");
            $qb->setParameter('startDate', $criteria->getFilter()['inventory:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['inventory:transactionDate'][2]);
        });
        $paperInventories = $inventoryRepository->findPaperInventories($papers, $criteria->getFilter()['inventory:transactionDate'][1], $criteria->getFilter()['inventory:transactionDate'][2]);
        $inventories = [];
        foreach ($paperInventories as $paperInventory) {
            $inventories[$paperInventory->getPaper()->getId()][] = $paperInventory;
        }

        return $this->renderForm("report/inventory_stock_paper/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'papers' => $papers,
            'inventories' => $inventories,
        ]);
    }

    #[Route('/', name: 'app_report_inventory_stock_paper_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_REPORT')]
    public function index(): Response
    {
        return $this->render("report/inventory_stock_paper/index.html.twig");
    }
}