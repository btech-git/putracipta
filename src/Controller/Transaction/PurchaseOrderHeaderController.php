<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseOrderHeader;
use App\Form\Transaction\PurchaseOrderHeaderType;
use App\Grid\Transaction\PurchaseOrderHeaderGridType;
use App\Repository\Transaction\PurchaseOrderHeaderRepository;
use App\Service\Transaction\PurchaseOrderHeaderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_order_header')]
class PurchaseOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_order_header__list', methods: ['GET'])]
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

    #[Route('/', name: 'app_transaction_purchase_order_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_order_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_transaction_purchase_order_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseOrderHeaderFormService $purchaseOrderHeaderFormService, $_format = 'html'): Response
    {
        $purchaseOrderHeader = new PurchaseOrderHeader();
        $purchaseOrderHeaderFormService->initialize($purchaseOrderHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);
        $purchaseOrderHeaderFormService->finalize($purchaseOrderHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderFormService->save($purchaseOrderHeader);

            return $this->redirectToRoute('app_transaction_purchase_order_header_show', ['id' => $purchaseOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_order_header/new.{$_format}.twig", [
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_order_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseOrderHeader $purchaseOrderHeader): Response
    {
        return $this->render('transaction/purchase_order_header/show.html.twig', [
            'purchaseOrderHeader' => $purchaseOrderHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_purchase_order_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderFormService $purchaseOrderHeaderFormService, $_format = 'html'): Response
    {
        $purchaseOrderHeaderFormService->initialize($purchaseOrderHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);
        $purchaseOrderHeaderFormService->finalize($purchaseOrderHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderFormService->save($purchaseOrderHeader);

            return $this->redirectToRoute('app_transaction_purchase_order_header_show', ['id' => $purchaseOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_order_header/edit.{$_format}.twig", [
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_order_header_delete', methods: ['POST'])]
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
    
    #[Route('/{id}/approve', name: 'app_transaction_purchase_order_header_approve', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function approve(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('approve' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeader->setApprovedTransactionDateTime(new \DateTime());
            $purchaseOrderHeader->setApprovedTransactionUser($this->getUser());
            $purchaseOrderHeader->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_APPROVE);
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The transaction was approved successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to approve the transaction.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/reject', name: 'app_transaction_purchase_order_header_reject', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function reject(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('reject' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeader->setRejectedTransactionDateTime(new \DateTime());
            $purchaseOrderHeader->setRejectedTransactionUser($this->getUser());
            $purchaseOrderHeader->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_REJECT);
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The transaction was rejected successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to reject the transaction.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_order_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/memo', name: 'app_transaction_purchase_order_header_memo', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memo(PurchaseOrderHeader $purchaseOrderHeader): Response
    {
        $fileName = 'purchase-order.pdf';
        $htmlView = $this->renderView('transaction/purchase_order_header/memo.html.twig', [
            'purchaseOrderHeader' => $purchaseOrderHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/assets/styles/', 'memo.css');
        $pdfGenerator->generate($htmlView, $fileName);
    }
}
