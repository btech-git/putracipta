<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Entity\Transaction\PurchaseOrderPaperHeader;
use App\Form\Transaction\PurchaseOrderPaperHeaderType;
use App\Grid\Transaction\PurchaseOrderPaperHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Transaction\PurchaseOrderPaperHeaderRepository;
use App\Service\Transaction\PurchaseOrderPaperHeaderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_order_paper_header')]
class PurchaseOrderPaperHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_order_paper_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseOrderPaperHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseOrderPaperHeaders) = $purchaseOrderPaperHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->query->get('purchase_order_paper_header_grid')['filter']['supplier:company']) && isset($request->query->get('purchase_order_paper_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->query->get('purchase_order_paper_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->query->get('purchase_order_paper_header_grid')['sort']['supplier:company']);
            }
        });

        return $this->renderForm("transaction/purchase_order_paper_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_order_paper_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_order_paper_header/index.html.twig");
    }

    #[Route('/_head', name: 'app_transaction_purchase_order_paper_header__head', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _head(Request $request, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['method' => 'GET', 'data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseOrderPaperHeaders) = $purchaseOrderPaperHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });

        return $this->renderForm("transaction/purchase_order_paper_header/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderPaperHeaders' => $purchaseOrderPaperHeaders,
        ]);
    }

    #[Route('/head', name: 'app_transaction_purchase_order_paper_header_head', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function head(): Response
    {
        return $this->render("transaction/purchase_order_paper_header/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_transaction_purchase_order_paper_header_read', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function read(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeader->setIsRead(true);
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader, true);
        }

        return $this->redirectToRoute('app_transaction_purchase_order_paper_header_show', ['id' => $purchaseOrderPaperHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_transaction_purchase_order_paper_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseOrderPaperHeaderFormService $purchaseOrderPaperHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseOrderPaperHeader = new PurchaseOrderPaperHeader();
        $purchaseOrderPaperHeaderFormService->initialize($purchaseOrderPaperHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderPaperHeaderType::class, $purchaseOrderPaperHeader);
        $form->handleRequest($request);
        $purchaseOrderPaperHeaderFormService->finalize($purchaseOrderPaperHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderPaperHeaderFormService->save($purchaseOrderPaperHeader);

            return $this->redirectToRoute('app_transaction_purchase_order_paper_header_show', ['id' => $purchaseOrderPaperHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_order_paper_header/new.{$_format}.twig", [
            'purchaseOrderPaperHeader' => $purchaseOrderPaperHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_order_paper_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseOrderPaperHeader $purchaseOrderPaperHeader): Response
    {
        return $this->render('transaction/purchase_order_paper_header/show.html.twig', [
            'purchaseOrderPaperHeader' => $purchaseOrderPaperHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_purchase_order_paper_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderFormService $purchaseOrderPaperHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $purchaseOrderPaperHeaderFormService->initialize($purchaseOrderPaperHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderPaperHeaderType::class, $purchaseOrderPaperHeader);
        $form->handleRequest($request);
        $purchaseOrderPaperHeaderFormService->finalize($purchaseOrderPaperHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderPaperHeaderFormService->save($purchaseOrderPaperHeader);

            return $this->redirectToRoute('app_transaction_purchase_order_paper_header_show', ['id' => $purchaseOrderPaperHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_order_paper_header/edit.{$_format}.twig", [
            'purchaseOrderPaperHeader' => $purchaseOrderPaperHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_order_paper_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeaderRepository->remove($purchaseOrderPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/approve', name: 'app_transaction_purchase_order_paper_header_approve', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function approve(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('approve' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeader->setApprovedTransactionDateTime(new \DateTime());
            $purchaseOrderPaperHeader->setApprovedTransactionUser($this->getUser());
            $purchaseOrderPaperHeader->setTransactionStatus(PurchaseOrderPaperHeader::TRANSACTION_STATUS_APPROVE);
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The transaction was approved successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to approve the transaction.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/reject', name: 'app_transaction_purchase_order_paper_header_reject', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function reject(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('reject' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeader->setRejectedTransactionDateTime(new \DateTime());
            $purchaseOrderPaperHeader->setRejectedTransactionUser($this->getUser());
            $purchaseOrderPaperHeader->setTransactionStatus(PurchaseOrderPaperHeader::TRANSACTION_STATUS_REJECT);
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The transaction was rejected successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to reject the transaction.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/hold', name: 'app_transaction_purchase_order_paper_header_hold', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function hold(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('hold' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeader->setIsOnHold(true);
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The transaction was hold successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to hold the transaction.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/release', name: 'app_transaction_purchase_order_paper_header_release', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function release(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('release' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeader->setIsOnHold(false);
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The transaction was release successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to release the transaction.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/complete', name: 'app_transaction_purchase_order_paper_header_complete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function complete(Request $request, PurchaseOrderPaperHeader $purchaseOrderPaperHeader, PurchaseOrderPaperHeaderRepository $purchaseOrderPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('complete' . $purchaseOrderPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseOrderPaperHeader->setTransactionStatus(PurchaseOrderPaperHeader::TRANSACTION_STATUS_FULL_RECEIVE);
            $purchaseOrderPaperHeaderRepository->add($purchaseOrderPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The transaction was closed successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to closed the transaction.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_order_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/memo', name: 'app_transaction_purchase_order_paper_header_memo', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memo(PurchaseOrderPaperHeader $purchaseOrderPaperHeader): Response
    {
        $fileName = 'purchase-order.pdf';
        $htmlView = $this->renderView('transaction/purchase_order_paper_header/memo.html.twig', [
            'purchaseOrderPaperHeader' => $purchaseOrderPaperHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link(.+)href=".+">/', '<link\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img(.+)src=".+">/', '<img\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
        ]);
    }
}
