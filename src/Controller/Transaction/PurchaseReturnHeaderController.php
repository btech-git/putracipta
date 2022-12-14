<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseReturnHeader;
use App\Form\Transaction\PurchaseReturnHeaderType;
use App\Grid\Transaction\PurchaseReturnHeaderGridType;
use App\Repository\Transaction\PurchaseReturnHeaderRepository;
use App\Service\Transaction\PurchaseReturnHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_return_header')]
class PurchaseReturnHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_return_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseReturnHeaderRepository $purchaseReturnHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseReturnHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseReturnHeaders) = $purchaseReturnHeaderRepository->fetchData($criteria);

        return $this->renderForm("transaction/purchase_return_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseReturnHeaders' => $purchaseReturnHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_return_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_return_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_transaction_purchase_return_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseReturnHeaderFormService $purchaseReturnHeaderFormService, $_format = 'html'): Response
    {
        $purchaseReturnHeader = new PurchaseReturnHeader();
        $purchaseReturnHeaderFormService->initialize($purchaseReturnHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseReturnHeaderType::class, $purchaseReturnHeader);
        $form->handleRequest($request);
        $purchaseReturnHeaderFormService->finalize($purchaseReturnHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseReturnHeaderFormService->save($purchaseReturnHeader);

            return $this->redirectToRoute('app_transaction_purchase_return_header_show', ['id' => $purchaseReturnHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_return_header/new.{$_format}.twig", [
            'purchaseReturnHeader' => $purchaseReturnHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_return_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseReturnHeader $purchaseReturnHeader): Response
    {
        return $this->render('transaction/purchase_return_header/show.html.twig', [
            'purchaseReturnHeader' => $purchaseReturnHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_purchase_return_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseReturnHeader $purchaseReturnHeader, PurchaseReturnHeaderFormService $purchaseReturnHeaderFormService, $_format = 'html'): Response
    {
        $purchaseReturnHeaderFormService->initialize($purchaseReturnHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseReturnHeaderType::class, $purchaseReturnHeader);
        $form->handleRequest($request);
        $purchaseReturnHeaderFormService->finalize($purchaseReturnHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseReturnHeaderFormService->save($purchaseReturnHeader);

            return $this->redirectToRoute('app_transaction_purchase_return_header_show', ['id' => $purchaseReturnHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_return_header/edit.{$_format}.twig", [
            'purchaseReturnHeader' => $purchaseReturnHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_return_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchaseReturnHeader $purchaseReturnHeader, PurchaseReturnHeaderRepository $purchaseReturnHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchaseReturnHeader->getId(), $request->request->get('_token'))) {
            $purchaseReturnHeaderRepository->remove($purchaseReturnHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_return_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
