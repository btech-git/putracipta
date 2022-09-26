<?php

namespace App\Controller\Transaction;

use App\Entity\Transaction\PurchaseInvoiceHeader;
use App\Form\Transaction\PurchaseInvoiceHeaderType;
use App\Repository\Transaction\PurchaseInvoiceHeaderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase/invoice/header')]
class PurchaseInvoiceHeaderController extends AbstractController
{
    #[Route('/', name: 'app_transaction_purchase_invoice_header_index', methods: ['GET'])]
    public function index(PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        return $this->render('transaction/purchase_invoice_header/index.html.twig', [
            'purchase_invoice_headers' => $purchaseInvoiceHeaderRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_transaction_purchase_invoice_header_new', methods: ['GET', 'POST'])]
    public function new(Request $request, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        $purchaseInvoiceHeader = new PurchaseInvoiceHeader();
        $form = $this->createForm(PurchaseInvoiceHeaderType::class, $purchaseInvoiceHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader, true);

            return $this->redirectToRoute('app_transaction_purchase_invoice_header_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_invoice_header/new.html.twig', [
            'purchase_invoice_header' => $purchaseInvoiceHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_invoice_header_show', methods: ['GET'])]
    public function show(PurchaseInvoiceHeader $purchaseInvoiceHeader): Response
    {
        return $this->render('transaction/purchase_invoice_header/show.html.twig', [
            'purchase_invoice_header' => $purchaseInvoiceHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_purchase_invoice_header_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        $form = $this->createForm(PurchaseInvoiceHeaderType::class, $purchaseInvoiceHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader, true);

            return $this->redirectToRoute('app_transaction_purchase_invoice_header_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_invoice_header/edit.html.twig', [
            'purchase_invoice_header' => $purchaseInvoiceHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_invoice_header_delete', methods: ['POST'])]
    public function delete(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$purchaseInvoiceHeader->getId(), $request->request->get('_token'))) {
            $purchaseInvoiceHeaderRepository->remove($purchaseInvoiceHeader, true);
        }

        return $this->redirectToRoute('app_transaction_purchase_invoice_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
