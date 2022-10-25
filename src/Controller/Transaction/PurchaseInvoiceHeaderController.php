<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseInvoiceHeader;
use App\Form\Transaction\PurchaseInvoiceHeaderType;
use App\Grid\Transaction\PurchaseInvoiceHeaderGridType;
use App\Repository\Transaction\PurchaseInvoiceHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_invoice_header')]
class PurchaseInvoiceHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_invoice_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseInvoiceHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseInvoiceHeaders) = $purchaseInvoiceHeaderRepository->fetchData($criteria);

        return $this->renderForm("transaction/purchase_invoice_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseInvoiceHeaders' => $purchaseInvoiceHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_invoice_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_invoice_header/index.html.twig");
    }

    #[Route('/new', name: 'app_transaction_purchase_invoice_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        $purchaseInvoiceHeader = new PurchaseInvoiceHeader();
        $form = $this->createForm(PurchaseInvoiceHeaderType::class, $purchaseInvoiceHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader, true);

            return $this->redirectToRoute('app_transaction_purchase_invoice_header_show', ['id' => $purchaseInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_invoice_header/new.html.twig', [
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_invoice_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseInvoiceHeader $purchaseInvoiceHeader): Response
    {
        return $this->render('transaction/purchase_invoice_header/show.html.twig', [
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_purchase_invoice_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        $form = $this->createForm(PurchaseInvoiceHeaderType::class, $purchaseInvoiceHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader, true);

            return $this->redirectToRoute('app_transaction_purchase_invoice_header_show', ['id' => $purchaseInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_invoice_header/edit.html.twig', [
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_invoice_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchaseInvoiceHeader->getId(), $request->request->get('_token'))) {
            $purchaseInvoiceHeaderRepository->remove($purchaseInvoiceHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_invoice_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
