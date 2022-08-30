<?php

namespace App\Controller\Transaction;

use App\Entity\Transaction\PurchaseOrderHeader;
use App\Form\Transaction\PurchaseOrderHeaderType;
use App\Repository\Transaction\PurchaseOrderHeaderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase/order/header')]
class PurchaseOrderHeaderController extends AbstractController
{
    #[Route('/', name: 'app_transaction_purchase_order_header_index', methods: ['GET'])]
    public function index(PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        return $this->render('transaction/purchase_order_header/index.html.twig', [
            'purchase_order_headers' => $purchaseOrderHeaderRepository->findAll(),
        ]);
    }

    #[Route('/new.{_format}', name: 'app_transaction_purchase_order_header_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository, $_format = 'html'): Response
    {
        $purchaseOrderHeader = new PurchaseOrderHeader();
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);

            return $this->redirectToRoute('app_transaction_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_order_header/new.{$_format}.twig", [
            'purchase_order_header' => $purchaseOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_order_header_show', methods: ['GET'])]
    public function show(PurchaseOrderHeader $purchaseOrderHeader): Response
    {
        return $this->render('transaction/purchase_order_header/show.html.twig', [
            'purchase_order_header' => $purchaseOrderHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_purchase_order_header_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);

            return $this->redirectToRoute('app_transaction_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_order_header/edit.html.twig', [
            'purchase_order_header' => $purchaseOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_order_header_delete', methods: ['POST'])]
    public function delete(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeaderRepository->remove($purchaseOrderHeader, true);
        }

        return $this->redirectToRoute('app_transaction_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
