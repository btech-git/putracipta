<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseOrderHeader;
use App\Form\Transaction\PurchaseOrderHeaderType;
use App\Grid\Transaction\PurchaseOrderHeaderGridType;
use App\Repository\Transaction\PurchaseOrderHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_order_header_2')]
class PurchaseOrderHeaderController_2 extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_order_header_2__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseOrderHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseOrderHeaders) = $purchaseOrderHeaderRepository->fetchData($criteria);

        return $this->renderForm("transaction/purchase_order_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderHeaders' => $purchaseOrderHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_order_header_2_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_order_header/index.html.twig");
    }

    #[Route('/new', name: 'app_transaction_purchase_order_header_2_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        $purchaseOrderHeader = new PurchaseOrderHeader();
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);

            return $this->redirectToRoute('app_transaction_purchase_order_header_show', ['id' => $purchaseOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_order_header/new.html.twig', [
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_order_header_2_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseOrderHeader $purchaseOrderHeader): Response
    {
        return $this->render('transaction/purchase_order_header/show.html.twig', [
            'purchaseOrderHeader' => $purchaseOrderHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_purchase_order_header_2_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);

            return $this->redirectToRoute('app_transaction_purchase_order_header_show', ['id' => $purchaseOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_order_header/edit.html.twig', [
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_order_header_2_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeaderRepository->remove($purchaseOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
