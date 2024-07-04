<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\InventoryRequestPaperDetailGridType;
use App\Repository\Stock\InventoryRequestPaperDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/inventory_request_paper_detail')]
class InventoryRequestPaperDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_inventory_request_paper_detail__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, InventoryRequestPaperDetailRepository $inventoryRequestPaperDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(InventoryRequestPaperDetailGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryRequestPaperDetails) = $inventoryRequestPaperDetailRepository->fetchData($criteria, function($qb, $alias, $add, $new) use ($request) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.quantityRemaining > 0");
        });

        return $this->renderForm("shared/inventory_request_paper_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryRequestPaperDetails' => $inventoryRequestPaperDetails,
        ]);
    }
}
