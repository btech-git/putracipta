<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Stock\AdjustmentStockHeader;
use App\Form\Stock\AdjustmentStockHeaderType;
use App\Grid\Stock\AdjustmentStockHeaderGridType;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/adjustment_stock_header')]
class AdjustmentStockHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_adjustment_stock_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(AdjustmentStockHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $adjustmentStockHeaders) = $adjustmentStockHeaderRepository->fetchData($criteria);

        return $this->renderForm("stock/adjustment_stock_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'adjustmentStockHeaders' => $adjustmentStockHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_adjustment_stock_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("stock/adjustment_stock_header/index.html.twig");
    }

    #[Route('/new', name: 'app_stock_adjustment_stock_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository): Response
    {
        $adjustmentStockHeader = new AdjustmentStockHeader();
        $form = $this->createForm(AdjustmentStockHeaderType::class, $adjustmentStockHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adjustmentStockHeaderRepository->add($adjustmentStockHeader, true);

            return $this->redirectToRoute('app_stock_adjustment_stock_header_show', ['id' => $adjustmentStockHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/adjustment_stock_header/new.html.twig', [
            'adjustmentStockHeader' => $adjustmentStockHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_adjustment_stock_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(AdjustmentStockHeader $adjustmentStockHeader): Response
    {
        return $this->render('stock/adjustment_stock_header/show.html.twig', [
            'adjustmentStockHeader' => $adjustmentStockHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_adjustment_stock_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, AdjustmentStockHeader $adjustmentStockHeader, AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository): Response
    {
        $form = $this->createForm(AdjustmentStockHeaderType::class, $adjustmentStockHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $adjustmentStockHeaderRepository->add($adjustmentStockHeader, true);

            return $this->redirectToRoute('app_stock_adjustment_stock_header_show', ['id' => $adjustmentStockHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/adjustment_stock_header/edit.html.twig', [
            'adjustmentStockHeader' => $adjustmentStockHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_adjustment_stock_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, AdjustmentStockHeader $adjustmentStockHeader, AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $adjustmentStockHeader->getId(), $request->request->get('_token'))) {
            $adjustmentStockHeaderRepository->remove($adjustmentStockHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_adjustment_stock_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
