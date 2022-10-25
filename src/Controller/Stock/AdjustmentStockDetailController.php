<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Stock\AdjustmentStockDetail;
use App\Form\Stock\AdjustmentStockDetailType;
use App\Grid\Stock\AdjustmentStockDetailGridType;
use App\Repository\Stock\AdjustmentStockDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/adjustment_stock_detail')]
class AdjustmentStockDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_adjustment_stock_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, AdjustmentStockDetailRepository $adjustmentStockDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(AdjustmentStockDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $adjustmentStockDetails) = $adjustmentStockDetailRepository->fetchData($criteria);

        return $this->renderForm("stock/adjustment_stock_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'adjustmentStockDetails' => $adjustmentStockDetails,
        ]);
    }

    #[Route('/', name: 'app_stock_adjustment_stock_detail_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("stock/adjustment_stock_detail/index.html.twig");
    }

    #[Route('/new', name: 'app_stock_adjustment_stock_detail_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, AdjustmentStockDetailRepository $adjustmentStockDetailRepository): Response
    {
        $adjustmentStockDetail = new AdjustmentStockDetail();
        $form = $this->createForm(AdjustmentStockDetailType::class, $adjustmentStockDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adjustmentStockDetailRepository->add($adjustmentStockDetail, true);

            return $this->redirectToRoute('app_stock_adjustment_stock_detail_show', ['id' => $adjustmentStockDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/adjustment_stock_detail/new.html.twig', [
            'adjustmentStockDetail' => $adjustmentStockDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_adjustment_stock_detail_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(AdjustmentStockDetail $adjustmentStockDetail): Response
    {
        return $this->render('stock/adjustment_stock_detail/show.html.twig', [
            'adjustmentStockDetail' => $adjustmentStockDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_adjustment_stock_detail_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, AdjustmentStockDetail $adjustmentStockDetail, AdjustmentStockDetailRepository $adjustmentStockDetailRepository): Response
    {
        $form = $this->createForm(AdjustmentStockDetailType::class, $adjustmentStockDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adjustmentStockDetailRepository->add($adjustmentStockDetail, true);

            return $this->redirectToRoute('app_stock_adjustment_stock_detail_show', ['id' => $adjustmentStockDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/adjustment_stock_detail/edit.html.twig', [
            'adjustmentStockDetail' => $adjustmentStockDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_adjustment_stock_detail_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, AdjustmentStockDetail $adjustmentStockDetail, AdjustmentStockDetailRepository $adjustmentStockDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $adjustmentStockDetail->getId(), $request->request->get('_token'))) {
            $adjustmentStockDetailRepository->remove($adjustmentStockDetail, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_adjustment_stock_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
