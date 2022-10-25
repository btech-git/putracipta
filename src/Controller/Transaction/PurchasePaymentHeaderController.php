<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchasePaymentHeader;
use App\Form\Transaction\PurchasePaymentHeaderType;
use App\Grid\Transaction\PurchasePaymentHeaderGridType;
use App\Repository\Transaction\PurchasePaymentHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_payment_header')]
class PurchasePaymentHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_payment_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchasePaymentHeaderRepository $purchasePaymentHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchasePaymentHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchasePaymentHeaders) = $purchasePaymentHeaderRepository->fetchData($criteria);

        return $this->renderForm("transaction/purchase_payment_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchasePaymentHeaders' => $purchasePaymentHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_payment_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_payment_header/index.html.twig");
    }

    #[Route('/new', name: 'app_transaction_purchase_payment_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchasePaymentHeaderRepository $purchasePaymentHeaderRepository): Response
    {
        $purchasePaymentHeader = new PurchasePaymentHeader();
        $form = $this->createForm(PurchasePaymentHeaderType::class, $purchasePaymentHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchasePaymentHeaderRepository->add($purchasePaymentHeader, true);

            return $this->redirectToRoute('app_transaction_purchase_payment_header_show', ['id' => $purchasePaymentHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_payment_header/new.html.twig', [
            'purchasePaymentHeader' => $purchasePaymentHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_payment_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchasePaymentHeader $purchasePaymentHeader): Response
    {
        return $this->render('transaction/purchase_payment_header/show.html.twig', [
            'purchasePaymentHeader' => $purchasePaymentHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_purchase_payment_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchasePaymentHeader $purchasePaymentHeader, PurchasePaymentHeaderRepository $purchasePaymentHeaderRepository): Response
    {
        $form = $this->createForm(PurchasePaymentHeaderType::class, $purchasePaymentHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchasePaymentHeaderRepository->add($purchasePaymentHeader, true);

            return $this->redirectToRoute('app_transaction_purchase_payment_header_show', ['id' => $purchasePaymentHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_payment_header/edit.html.twig', [
            'purchasePaymentHeader' => $purchasePaymentHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_payment_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchasePaymentHeader $purchasePaymentHeader, PurchasePaymentHeaderRepository $purchasePaymentHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchasePaymentHeader->getId(), $request->request->get('_token'))) {
            $purchasePaymentHeaderRepository->remove($purchasePaymentHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_payment_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
