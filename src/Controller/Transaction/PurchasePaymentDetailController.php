<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchasePaymentDetail;
use App\Form\Transaction\PurchasePaymentDetailType;
use App\Grid\Transaction\PurchasePaymentDetailGridType;
use App\Repository\Transaction\PurchasePaymentDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_payment_detail')]
class PurchasePaymentDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_payment_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchasePaymentDetailRepository $purchasePaymentDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchasePaymentDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchasePaymentDetails) = $purchasePaymentDetailRepository->fetchData($criteria);

        return $this->renderForm("transaction/purchase_payment_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchasePaymentDetails' => $purchasePaymentDetails,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_payment_detail_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_payment_detail/index.html.twig");
    }

    #[Route('/new', name: 'app_transaction_purchase_payment_detail_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchasePaymentDetailRepository $purchasePaymentDetailRepository): Response
    {
        $purchasePaymentDetail = new PurchasePaymentDetail();
        $form = $this->createForm(PurchasePaymentDetailType::class, $purchasePaymentDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchasePaymentDetailRepository->add($purchasePaymentDetail, true);

            return $this->redirectToRoute('app_transaction_purchase_payment_detail_show', ['id' => $purchasePaymentDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_payment_detail/new.html.twig', [
            'purchasePaymentDetail' => $purchasePaymentDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_payment_detail_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchasePaymentDetail $purchasePaymentDetail): Response
    {
        return $this->render('transaction/purchase_payment_detail/show.html.twig', [
            'purchasePaymentDetail' => $purchasePaymentDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_purchase_payment_detail_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchasePaymentDetail $purchasePaymentDetail, PurchasePaymentDetailRepository $purchasePaymentDetailRepository): Response
    {
        $form = $this->createForm(PurchasePaymentDetailType::class, $purchasePaymentDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchasePaymentDetailRepository->add($purchasePaymentDetail, true);

            return $this->redirectToRoute('app_transaction_purchase_payment_detail_show', ['id' => $purchasePaymentDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_payment_detail/edit.html.twig', [
            'purchasePaymentDetail' => $purchasePaymentDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_payment_detail_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchasePaymentDetail $purchasePaymentDetail, PurchasePaymentDetailRepository $purchasePaymentDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchasePaymentDetail->getId(), $request->request->get('_token'))) {
            $purchasePaymentDetailRepository->remove($purchasePaymentDetail, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_payment_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
