<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseOrderDetail;
use App\Form\Transaction\PurchaseOrderDetailType;
use App\Grid\Transaction\PurchaseOrderDetailGridType;
use App\Repository\Transaction\PurchaseOrderDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_order_detail')]
class PurchaseOrderDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_order_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseOrderDetailRepository $purchaseOrderDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseOrderDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseOrderDetails) = $purchaseOrderDetailRepository->fetchData($criteria);

        return $this->renderForm("transaction/purchase_order_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderDetails' => $purchaseOrderDetails,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_order_detail_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_order_detail/index.html.twig");
    }

    #[Route('/new', name: 'app_transaction_purchase_order_detail_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseOrderDetailRepository $purchaseOrderDetailRepository): Response
    {
        $purchaseOrderDetail = new PurchaseOrderDetail();
        $form = $this->createForm(PurchaseOrderDetailType::class, $purchaseOrderDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseOrderDetailRepository->add($purchaseOrderDetail, true);

            return $this->redirectToRoute('app_transaction_purchase_order_detail_show', ['id' => $purchaseOrderDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_order_detail/new.html.twig', [
            'purchaseOrderDetail' => $purchaseOrderDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_order_detail_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseOrderDetail $purchaseOrderDetail): Response
    {
        return $this->render('transaction/purchase_order_detail/show.html.twig', [
            'purchaseOrderDetail' => $purchaseOrderDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_purchase_order_detail_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseOrderDetail $purchaseOrderDetail, PurchaseOrderDetailRepository $purchaseOrderDetailRepository): Response
    {
        $form = $this->createForm(PurchaseOrderDetailType::class, $purchaseOrderDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseOrderDetailRepository->add($purchaseOrderDetail, true);

            return $this->redirectToRoute('app_transaction_purchase_order_detail_show', ['id' => $purchaseOrderDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_order_detail/edit.html.twig', [
            'purchaseOrderDetail' => $purchaseOrderDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_order_detail_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchaseOrderDetail $purchaseOrderDetail, PurchaseOrderDetailRepository $purchaseOrderDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchaseOrderDetail->getId(), $request->request->get('_token'))) {
            $purchaseOrderDetailRepository->remove($purchaseOrderDetail, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_order_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
