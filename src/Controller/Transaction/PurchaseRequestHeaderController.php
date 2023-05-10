<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Entity\Transaction\PurchaseRequestHeader;
use App\Form\Transaction\PurchaseRequestHeaderType;
use App\Grid\Transaction\PurchaseRequestHeaderGridType;
use App\Repository\Transaction\PurchaseRequestHeaderRepository;
use App\Service\Transaction\PurchaseRequestHeaderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_request_header')]
class PurchaseRequestHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_request_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseRequestHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseRequestHeaders) = $purchaseRequestHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->query->get('purchase_request_header_grid')['sort']['warehouse:name'])) {
                $qb->innerJoin("{$alias}.warehouse", 'w');
                $add['sort']($qb, 'w', 'name', $request->query->get('purchase_request_header_grid')['sort']['warehouse:name']);
            }
        });

        return $this->renderForm("transaction/purchase_request_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestHeaders' => $purchaseRequestHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_request_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_request_header/index.html.twig");
    }

    #[Route('/_head', name: 'app_transaction_purchase_request_header__head', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _head(Request $request, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['method' => 'GET', 'data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseRequestHeaders) = $purchaseRequestHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });

        return $this->renderForm("transaction/purchase_request_header/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestHeaders' => $purchaseRequestHeaders,
        ]);
    }

    #[Route('/head', name: 'app_transaction_purchase_request_header_head', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function head(): Response
    {
        return $this->render("transaction/purchase_request_header/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_transaction_purchase_request_header_read', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function read(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $purchaseRequestHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestHeader->setIsRead(true);
            $purchaseRequestHeaderRepository->add($purchaseRequestHeader, true);
        }

        return $this->redirectToRoute('app_transaction_purchase_request_header_show', ['id' => $purchaseRequestHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_transaction_purchase_request_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseRequestHeaderFormService $purchaseRequestHeaderFormService, $_format = 'html'): Response
    {
        $purchaseRequestHeader = new PurchaseRequestHeader();
        $purchaseRequestHeaderFormService->initialize($purchaseRequestHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseRequestHeaderType::class, $purchaseRequestHeader);
        $form->handleRequest($request);
        $purchaseRequestHeaderFormService->finalize($purchaseRequestHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseRequestHeaderFormService->save($purchaseRequestHeader);

            return $this->redirectToRoute('app_transaction_purchase_request_header_show', ['id' => $purchaseRequestHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_request_header/new.{$_format}.twig", [
            'purchaseRequestHeader' => $purchaseRequestHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_request_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseRequestHeader $purchaseRequestHeader): Response
    {
        return $this->render('transaction/purchase_request_header/show.html.twig', [
            'purchaseRequestHeader' => $purchaseRequestHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_purchase_request_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderFormService $purchaseRequestHeaderFormService, $_format = 'html'): Response
    {
        $purchaseRequestHeaderFormService->initialize($purchaseRequestHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseRequestHeaderType::class, $purchaseRequestHeader);
        $form->handleRequest($request);
        $purchaseRequestHeaderFormService->finalize($purchaseRequestHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseRequestHeaderFormService->save($purchaseRequestHeader);

            return $this->redirectToRoute('app_transaction_purchase_request_header_show', ['id' => $purchaseRequestHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_request_header/edit.{$_format}.twig", [
            'purchaseRequestHeader' => $purchaseRequestHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_request_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchaseRequestHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestHeaderRepository->remove($purchaseRequestHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_request_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/approve', name: 'app_transaction_purchase_request_header_approve', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function approve(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('approve' . $purchaseRequestHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestHeader->setApprovedTransactionDateTime(new \DateTime());
            $purchaseRequestHeader->setApprovedTransactionUser($this->getUser());
            $purchaseRequestHeader->setTransactionStatus(PurchaseRequestHeader::TRANSACTION_STATUS_APPROVE);
            $purchaseRequestHeaderRepository->add($purchaseRequestHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The transaction was approved successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to approve the transaction.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_request_header_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/reject', name: 'app_transaction_purchase_request_header_reject', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function reject(Request $request, PurchaseRequestHeader $purchaseRequestHeader, PurchaseRequestHeaderRepository $purchaseRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('reject' . $purchaseRequestHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestHeader->setRejectedTransactionDateTime(new \DateTime());
            $purchaseRequestHeader->setRejectedTransactionUser($this->getUser());
            $purchaseRequestHeader->setTransactionStatus(PurchaseRequestHeader::TRANSACTION_STATUS_REJECT);
            $purchaseRequestHeaderRepository->add($purchaseRequestHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The transaction was rejected successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to reject the transaction.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_request_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/memo', name: 'app_transaction_purchase_request_header_memo', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memo(PurchaseRequestHeader $purchaseRequestHeader): Response
    {
        $fileName = 'purchase-request.pdf';
        $htmlView = $this->renderView('transaction/purchase_request_header/memo.html.twig', [
            'purchaseRequestHeader' => $purchaseRequestHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img(.+)src=".+">/', '<img\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
        ]);
    }
}
