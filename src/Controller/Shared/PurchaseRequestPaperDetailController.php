<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\PurchaseRequestPaperDetailGridType;
use App\Repository\Transaction\PurchaseRequestPaperDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/purchase_request_paper_detail')]
class PurchaseRequestPaperDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_purchase_request_paper_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseRequestPaperDetailRepository $purchaseRequestPaperDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseRequestPaperDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseRequestPaperDetails) = $purchaseRequestPaperDetailRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/purchase_request_paper_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestPaperDetails' => $purchaseRequestPaperDetails,
        ]);
    }
}
