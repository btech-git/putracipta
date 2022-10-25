<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseInvoiceDetail;
use App\Form\Transaction\PurchaseInvoiceDetailType;
use App\Grid\Transaction\PurchaseInvoiceDetailGridType;
use App\Repository\Transaction\PurchaseInvoiceDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_invoice_detail')]
class PurchaseInvoiceDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_invoice_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseInvoiceDetailRepository $purchaseInvoiceDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseInvoiceDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseInvoiceDetails) = $purchaseInvoiceDetailRepository->fetchData($criteria);

        return $this->renderForm("transaction/purchase_invoice_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseInvoiceDetails' => $purchaseInvoiceDetails,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_invoice_detail_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_invoice_detail/index.html.twig");
    }

    #[Route('/new', name: 'app_transaction_purchase_invoice_detail_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseInvoiceDetailRepository $purchaseInvoiceDetailRepository): Response
    {
        $purchaseInvoiceDetail = new PurchaseInvoiceDetail();
        $form = $this->createForm(PurchaseInvoiceDetailType::class, $purchaseInvoiceDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceDetailRepository->add($purchaseInvoiceDetail, true);

            return $this->redirectToRoute('app_transaction_purchase_invoice_detail_show', ['id' => $purchaseInvoiceDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_invoice_detail/new.html.twig', [
            'purchaseInvoiceDetail' => $purchaseInvoiceDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_invoice_detail_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseInvoiceDetail $purchaseInvoiceDetail): Response
    {
        return $this->render('transaction/purchase_invoice_detail/show.html.twig', [
            'purchaseInvoiceDetail' => $purchaseInvoiceDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_purchase_invoice_detail_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseInvoiceDetail $purchaseInvoiceDetail, PurchaseInvoiceDetailRepository $purchaseInvoiceDetailRepository): Response
    {
        $form = $this->createForm(PurchaseInvoiceDetailType::class, $purchaseInvoiceDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceDetailRepository->add($purchaseInvoiceDetail, true);

            return $this->redirectToRoute('app_transaction_purchase_invoice_detail_show', ['id' => $purchaseInvoiceDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_invoice_detail/edit.html.twig', [
            'purchaseInvoiceDetail' => $purchaseInvoiceDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_invoice_detail_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchaseInvoiceDetail $purchaseInvoiceDetail, PurchaseInvoiceDetailRepository $purchaseInvoiceDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchaseInvoiceDetail->getId(), $request->request->get('_token'))) {
            $purchaseInvoiceDetailRepository->remove($purchaseInvoiceDetail, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_invoice_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
