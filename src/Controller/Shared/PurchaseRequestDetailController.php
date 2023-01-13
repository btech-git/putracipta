<?php

namespace App\Controller\Shared;

use App\Common\Data\Criteria\DataCriteria;
use App\Grid\Shared\PurchaseRequestDetailGridType;
use App\Repository\Transaction\PurchaseRequestDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/shared/purchase_request_detail')]
class PurchaseRequestDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_shared_purchase_request_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseRequestDetailRepository $purchaseRequestDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseRequestDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseRequestDetails) = $purchaseRequestDetailRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
        });

        return $this->renderForm("shared/purchase_request_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestDetails' => $purchaseRequestDetails,
        ]);
    }
}
