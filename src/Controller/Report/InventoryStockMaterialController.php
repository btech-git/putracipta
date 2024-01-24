<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\InventoryStockMaterialGridType;
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
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, InventoryRepository $inventoryRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(InventoryStockMaterialGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryStockMaterials) = $inventoryRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.product IS NOT null AND {$alias}.isReversed = 0");
        });

        return $this->renderForm("report/inventory_stock_material/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryStockMaterials' => $inventoryStockMaterials,
        ]);
    }

    #[Route('/', name: 'app_report_inventory_stock_material_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("report/inventory_stock_material/index.html.twig");
    }
}