<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Transaction\PurchaseRequestPaperHeader;
use App\Form\Transaction\PurchaseRequestPaperHeaderType;
use App\Grid\Transaction\PurchaseRequestPaperHeaderGridType;
use App\Repository\Transaction\PurchaseRequestPaperHeaderRepository;
use App\Service\Transaction\PurchaseRequestPaperHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_request_paper_header')]
class PurchaseRequestPaperHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_request_paper_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseRequestPaperHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseRequestPaperHeaders) = $purchaseRequestPaperHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->query->get('purchase_request_paper_header_grid')['filter']['warehouse:name']) && isset($request->query->get('purchase_request_paper_header_grid')['sort']['warehouse:name'])) {
                $qb->innerJoin("{$alias}.warehouse", 'w');
                $add['filter']($qb, 'w', 'name', $request->query->get('purchase_request_paper_header_grid')['filter']['warehouse:name']);
                $add['sort']($qb, 'w', 'name', $request->query->get('purchase_request_paper_header_grid')['sort']['warehouse:name']);
            }
        });

        return $this->renderForm("transaction/purchase_request_paper_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseRequestPaperHeaders' => $purchaseRequestPaperHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_request_paper_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_request_paper_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_transaction_purchase_request_paper_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseRequestPaperHeaderFormService $purchaseRequestPaperHeaderFormService, $_format = 'html'): Response
    {
        $purchaseRequestPaperHeader = new PurchaseRequestPaperHeader();
        $purchaseRequestPaperHeaderFormService->initialize($purchaseRequestPaperHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseRequestPaperHeaderType::class, $purchaseRequestPaperHeader);
        $form->handleRequest($request);
        $purchaseRequestPaperHeaderFormService->finalize($purchaseRequestPaperHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseRequestPaperHeaderFormService->save($purchaseRequestPaperHeader);

            return $this->redirectToRoute('app_transaction_purchase_request_paper_header_show', ['id' => $purchaseRequestPaperHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_request_paper_header/new.{$_format}.twig", [
            'purchaseRequestPaperHeader' => $purchaseRequestPaperHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_request_paper_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseRequestPaperHeader $purchaseRequestPaperHeader): Response
    {
        return $this->render('transaction/purchase_request_paper_header/show.html.twig', [
            'purchaseRequestPaperHeader' => $purchaseRequestPaperHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_purchase_request_paper_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseRequestPaperHeader $purchaseRequestPaperHeader, PurchaseRequestPaperHeaderFormService $purchaseRequestPaperHeaderFormService, $_format = 'html'): Response
    {
        $purchaseRequestPaperHeaderFormService->initialize($purchaseRequestPaperHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseRequestPaperHeaderType::class, $purchaseRequestPaperHeader);
        $form->handleRequest($request);
        $purchaseRequestPaperHeaderFormService->finalize($purchaseRequestPaperHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseRequestPaperHeaderFormService->save($purchaseRequestPaperHeader);

            return $this->redirectToRoute('app_transaction_purchase_request_paper_header_show', ['id' => $purchaseRequestPaperHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_request_paper_header/edit.{$_format}.twig", [
            'purchaseRequestPaperHeader' => $purchaseRequestPaperHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_request_paper_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchaseRequestPaperHeader $purchaseRequestPaperHeader, PurchaseRequestPaperHeaderRepository $purchaseRequestPaperHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchaseRequestPaperHeader->getId(), $request->request->get('_token'))) {
            $purchaseRequestPaperHeaderRepository->remove($purchaseRequestPaperHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_request_paper_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
