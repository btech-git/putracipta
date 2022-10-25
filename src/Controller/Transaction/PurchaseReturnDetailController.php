<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseReturnDetail;
use App\Form\Transaction\PurchaseReturnDetailType;
use App\Grid\Transaction\PurchaseReturnDetailGridType;
use App\Repository\Transaction\PurchaseReturnDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_return_detail')]
class PurchaseReturnDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_return_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseReturnDetailRepository $purchaseReturnDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseReturnDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseReturnDetails) = $purchaseReturnDetailRepository->fetchData($criteria);

        return $this->renderForm("transaction/purchase_return_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseReturnDetails' => $purchaseReturnDetails,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_return_detail_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_return_detail/index.html.twig");
    }

    #[Route('/new', name: 'app_transaction_purchase_return_detail_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseReturnDetailRepository $purchaseReturnDetailRepository): Response
    {
        $purchaseReturnDetail = new PurchaseReturnDetail();
        $form = $this->createForm(PurchaseReturnDetailType::class, $purchaseReturnDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseReturnDetailRepository->add($purchaseReturnDetail, true);

            return $this->redirectToRoute('app_transaction_purchase_return_detail_show', ['id' => $purchaseReturnDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_return_detail/new.html.twig', [
            'purchaseReturnDetail' => $purchaseReturnDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_return_detail_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseReturnDetail $purchaseReturnDetail): Response
    {
        return $this->render('transaction/purchase_return_detail/show.html.twig', [
            'purchaseReturnDetail' => $purchaseReturnDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_purchase_return_detail_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseReturnDetail $purchaseReturnDetail, PurchaseReturnDetailRepository $purchaseReturnDetailRepository): Response
    {
        $form = $this->createForm(PurchaseReturnDetailType::class, $purchaseReturnDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $purchaseReturnDetailRepository->add($purchaseReturnDetail, true);

            return $this->redirectToRoute('app_transaction_purchase_return_detail_show', ['id' => $purchaseReturnDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/purchase_return_detail/edit.html.twig', [
            'purchaseReturnDetail' => $purchaseReturnDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_purchase_return_detail_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchaseReturnDetail $purchaseReturnDetail, PurchaseReturnDetailRepository $purchaseReturnDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchaseReturnDetail->getId(), $request->request->get('_token'))) {
            $purchaseReturnDetailRepository->remove($purchaseReturnDetail, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_purchase_return_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
