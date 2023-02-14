<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseRequestHeader;
use App\Form\Transaction\PurchaseRequestHeaderType;
use App\Grid\Transaction\PurchaseRequestHeaderGridType;
use App\Repository\Transaction\PurchaseRequestHeaderRepository;
use App\Service\Transaction\PurchaseRequestHeaderFormService;
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
        $form = $this->createForm(PurchaseRequestHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseRequestHeaders) = $purchaseRequestHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->addOrderBy("{$alias}.transactionDate", 'DESC');
            $qb->addOrderBy("{$alias}.id", 'DESC');
            if (isset($request->query->get('purchase_request_header_grid')['filter']['warehouse:name']) && isset($request->query->get('purchase_request_header_grid')['sort']['warehouse:name'])) {
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

    #[Route('/new.{_format}', name: 'app_transaction_purchase_request_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseRequestHeaderFormService $purchaseRequestHeaderFormService, $_format = 'html'): Response
    {
        $purchaseRequestHeader = new PurchaseRequestHeader();
        $purchaseRequestHeaderFormService->initialize($purchaseRequestHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
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
        $purchaseRequestHeaderFormService->initialize($purchaseRequestHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
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
}
