<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Stock\AdjustmentStockHeader;
use App\Form\Stock\AdjustmentStockHeaderType;
use App\Grid\Stock\AdjustmentStockHeaderGridType;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
use App\Service\Stock\AdjustmentStockHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/adjustment_stock_header')]
class AdjustmentStockHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_adjustment_stock_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_ADJUSTMENT_ADD') or is_granted('ROLE_ADJUSTMENT_EDIT') or is_granted('ROLE_ADJUSTMENT_VIEW')")]
    public function _list(Request $request, AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(AdjustmentStockHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $adjustmentStockHeaders) = $adjustmentStockHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
//            if (isset($request->request->get('adjustment_stock_header_grid')['filter']['warehouse:name']) && isset($request->request->get('adjustment_stock_header_grid')['sort']['warehouse:name'])) {
//                $qb->innerJoin("{$alias}.warehouse", 'w');
//                $add['filter']($qb, 'w', 'name', $request->request->get('adjustment_stock_header_grid')['filter']['warehouse:name']);
//                $add['sort']($qb, 'w', 'name', $request->request->get('adjustment_stock_header_grid')['sort']['warehouse:name']);
//            }
        });

        return $this->renderForm("stock/adjustment_stock_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'adjustmentStockHeaders' => $adjustmentStockHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_adjustment_stock_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADJUSTMENT_ADD') or is_granted('ROLE_ADJUSTMENT_EDIT') or is_granted('ROLE_ADJUSTMENT_VIEW')")]
    public function index(): Response
    {
        return $this->render("stock/adjustment_stock_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_stock_adjustment_stock_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADJUSTMENT_ADD')]
    public function new(Request $request, AdjustmentStockHeaderFormService $adjustmentStockHeaderFormService, $_format = 'html'): Response
    {
        $adjustmentStockHeader = new AdjustmentStockHeader();
        $adjustmentStockHeaderFormService->initialize($adjustmentStockHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(AdjustmentStockHeaderType::class, $adjustmentStockHeader);
        $form->handleRequest($request);
        $adjustmentStockHeaderFormService->finalize($adjustmentStockHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $adjustmentStockHeaderFormService->save($adjustmentStockHeader);

            return $this->redirectToRoute('app_stock_adjustment_stock_header_show', ['id' => $adjustmentStockHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/adjustment_stock_header/new.{$_format}.twig", [
            'adjustmentStockHeader' => $adjustmentStockHeader,
            'form' => $form,
        ]);
    }

    #[Route('/new.{_format}', name: 'app_stock_adjustment_stock_header_new_finished_goods', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADJUSTMENT_ADD')]
    public function newFinishedGoods(Request $request, AdjustmentStockHeaderFormService $adjustmentStockHeaderFormService, $_format = 'html'): Response
    {
        $adjustmentStockHeader = new AdjustmentStockHeader();
        $adjustmentStockHeaderFormService->initialize($adjustmentStockHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(AdjustmentStockHeaderType::class, $adjustmentStockHeader);
        $form->handleRequest($request);
        $adjustmentStockHeaderFormService->finalize($adjustmentStockHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $adjustmentStockHeaderFormService->save($adjustmentStockHeader);

            return $this->redirectToRoute('app_stock_adjustment_stock_header_show', ['id' => $adjustmentStockHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/adjustment_stock_header/new_finished_goods.{$_format}.twig", [
            'adjustmentStockHeader' => $adjustmentStockHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_adjustment_stock_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_ADJUSTMENT_ADD') or is_granted('ROLE_ADJUSTMENT_EDIT') or is_granted('ROLE_ADJUSTMENT_VIEW')")]
    public function show(AdjustmentStockHeader $adjustmentStockHeader): Response
    {
        return $this->render('stock/adjustment_stock_header/show.html.twig', [
            'adjustmentStockHeader' => $adjustmentStockHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_stock_adjustment_stock_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADJUSTMENT_EDIT')]
//    public function edit(Request $request, AdjustmentStockHeader $adjustmentStockHeader, AdjustmentStockHeaderFormService $adjustmentStockHeaderFormService, $_format = 'html'): Response
//    {
//        $adjustmentStockHeaderFormService->initialize($adjustmentStockHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
//        $form = $this->createForm(AdjustmentStockHeaderType::class, $adjustmentStockHeader);
//        $form->handleRequest($request);
//        $adjustmentStockHeaderFormService->finalize($adjustmentStockHeader);
//
//        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
//            $adjustmentStockHeaderFormService->save($adjustmentStockHeader);
//
//            return $this->redirectToRoute('app_stock_adjustment_stock_header_show', ['id' => $adjustmentStockHeader->getId()], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->renderForm("stock/adjustment_stock_header/edit.{$_format}.twig", [
//            'adjustmentStockHeader' => $adjustmentStockHeader,
//            'form' => $form,
//        ]);
//    }

    #[Route('/{id}/delete', name: 'app_stock_adjustment_stock_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADJUSTMENT_EDIT')]
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
