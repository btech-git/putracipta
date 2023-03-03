<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Entity\Transaction\PurchaseOrderHeader;
use App\Form\Transaction\PurchaseOrderHeaderType;
use App\Grid\Transaction\PurchaseOrderHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
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
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseOrderHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseOrderHeaders) = $purchaseOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->query->get('purchase_order_header_grid')['filter']['supplier:company']) && isset($request->query->get('purchase_order_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->query->get('purchase_order_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->query->get('purchase_order_header_grid')['sort']['supplier:company']);
            }
        });

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
    
    #[Route('/_head', name: 'app_transaction_purchase_order_header__head', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _head(Request $request, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['method' => 'GET', 'data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseOrderHeaders) = $purchaseOrderHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });

        return $this->renderForm("transaction/purchase_order_header/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderHeaders' => $purchaseOrderHeaders,
        ]);
    }

    #[Route('/head', name: 'app_transaction_purchase_order_header_head', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function head(): Response
    {
        return $this->render("transaction/purchase_order_header/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_transaction_purchase_order_header_read', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function read(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeader->setIsRead(true);
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);
        }

        return $this->redirectToRoute('app_transaction_purchase_order_header_show', ['id' => $purchaseOrderHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_transaction_purchase_order_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseOrderHeaderFormService $purchaseOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseOrderHeader = new PurchaseOrderHeader();
        $purchaseOrderHeaderFormService->initialize($purchaseOrderHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);
        $purchaseOrderHeaderFormService->finalize($purchaseOrderHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

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
    public function edit(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderFormService $purchaseOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseOrderHeaderFormService->initialize($purchaseOrderHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderHeaderType::class, $purchaseOrderHeader);
        $form->handleRequest($request);
        $purchaseOrderHeaderFormService->finalize($purchaseOrderHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

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

    #[Route('/{id}/hold', name: 'app_transaction_purchase_order_header_hold', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function hold(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('hold' . $purchaseOrderHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderHeader->setTransactionStatus(PurchaseOrderHeader::TRANSACTION_STATUS_HOLD);
            $purchaseOrderHeaderRepository->add($purchaseOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The transaction was hold successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to hold the transaction.'));
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

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link(.+)href=".+">/', '<link\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img(.+)src=".+">/', '<img\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
        ]);
    }
}
