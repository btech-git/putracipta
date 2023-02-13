<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseInvoiceHeader;
use App\Form\Transaction\PurchaseInvoiceHeaderType;
use App\Grid\Transaction\PurchaseInvoiceHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Transaction\PurchaseInvoiceHeaderRepository;
use App\Service\Transaction\PurchaseInvoiceHeaderFormService;
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

        list($count, $purchaseInvoiceHeaders) = $purchaseInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->addOrderBy("{$alias}.transactionDate", 'DESC');
            $qb->addOrderBy("{$alias}.id", 'DESC');
            if (isset($request->query->get('purchase_invoice_header_grid')['filter']['supplier:company']) && isset($request->query->get('purchase_invoice_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->query->get('purchase_invoice_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->query->get('purchase_invoice_header_grid')['sort']['supplier:company']);
            }
        });

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

    #[Route('/new.{_format}', name: 'app_transaction_purchase_invoice_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseInvoiceHeaderFormService $purchaseInvoiceHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseInvoiceHeader = new PurchaseInvoiceHeader();
        $purchaseInvoiceHeaderFormService->initialize($purchaseInvoiceHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseInvoiceHeaderType::class, $purchaseInvoiceHeader);
        $form->handleRequest($request);
        $purchaseInvoiceHeaderFormService->finalize($purchaseInvoiceHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderFormService->save($purchaseInvoiceHeader);

            return $this->redirectToRoute('app_transaction_purchase_invoice_header_show', ['id' => $purchaseInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_invoice_header/new.{$_format}.twig", [
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

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_purchase_invoice_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader, PurchaseInvoiceHeaderFormService $purchaseInvoiceHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseInvoiceHeaderFormService->initialize($purchaseInvoiceHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseInvoiceHeaderType::class, $purchaseInvoiceHeader);
        $form->handleRequest($request);
        $purchaseInvoiceHeaderFormService->finalize($purchaseInvoiceHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderFormService->save($purchaseInvoiceHeader);

            return $this->redirectToRoute('app_transaction_purchase_invoice_header_show', ['id' => $purchaseInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_invoice_header/edit.{$_format}.twig", [
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
