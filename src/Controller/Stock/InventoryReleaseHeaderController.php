<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Stock\InventoryReleaseHeader;
use App\Form\Stock\InventoryReleaseHeaderType;
use App\Grid\Stock\InventoryReleaseHeaderGridType;
use App\Repository\Stock\InventoryReleaseHeaderRepository;
use App\Service\Stock\InventoryReleaseHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/inventory_release_header')]
class InventoryReleaseHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_inventory_release_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_MATERIAL_RELEASE_ADD') or is_granted('ROLE_MATERIAL_RELEASE_EDIT')")]
    public function _list(Request $request, InventoryReleaseHeaderRepository $inventoryReleaseHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(InventoryReleaseHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryReleaseHeaders) = $inventoryReleaseHeaderRepository->fetchData($criteria);

        return $this->renderForm("stock/inventory_release_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryReleaseHeaders' => $inventoryReleaseHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_inventory_release_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_MATERIAL_RELEASE_ADD') or is_granted('ROLE_MATERIAL_RELEASE_EDIT')")]
    public function index(): Response
    {
        return $this->render("stock/inventory_release_header/index.html.twig");
    }

    #[Route('/_list_outstanding', name: 'app_stock_inventory_release_header__list_outstanding', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_DELIVERY_ADD') or is_granted('ROLE_DELIVERY_EDIT')")]
    public function _listOutstanding(Request $request, InventoryRequestMaterialDetailRepository $inventoryRequestMaterialDetailRepository, InventoryRequestPaperDetailRepository $inventoryRequestPaperDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $inventoryRequestMaterialDetails) = $inventoryRequestMaterialDetailRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.quantityRemaining > 0");
        });

        list($count, $inventoryRequestPaperDetails) = $inventoryRequestPaperDetailRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.quantityRemaining > 0");
        });

        return $this->renderForm("sale/delivery_header/_list_outstanding_sale_order.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryRequestMaterialDetails' => $inventoryRequestMaterialDetails,
            'inventoryRequestPaperDetails' => $inventoryRequestPaperDetails,
        ]);
    }

    #[Route('/index_outstanding', name: 'app_stock_inventory_release_header_index_outstanding', methods: ['GET'])]
    #[Security("is_granted('ROLE_DELIVERY_ADD') or is_granted('ROLE_DELIVERY_EDIT')")]
    public function indexOutstanding(): Response
    {
        return $this->render("stock/inventory_release_header/index_outstanding.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_stock_inventory_release_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MATERIAL_RELEASE_ADD')]
    public function new(Request $request, InventoryReleaseHeaderFormService $inventoryReleaseHeaderFormService, $_format = 'html'): Response
    {
        $inventoryReleaseHeader = new InventoryReleaseHeader();
        $inventoryReleaseHeaderFormService->initialize($inventoryReleaseHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryReleaseHeaderType::class, $inventoryReleaseHeader);
        $form->handleRequest($request);
        $inventoryReleaseHeaderFormService->finalize($inventoryReleaseHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $inventoryReleaseHeaderFormService->save($inventoryReleaseHeader);

            return $this->redirectToRoute('app_stock_inventory_release_header_show', ['id' => $inventoryReleaseHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_release_header/new.{$_format}.twig", [
            'inventoryReleaseHeader' => $inventoryReleaseHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_inventory_release_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_MATERIAL_RELEASE_ADD') or is_granted('ROLE_MATERIAL_RELEASE_EDIT')")]
    public function show(InventoryReleaseHeader $inventoryReleaseHeader): Response
    {
        return $this->render('stock/inventory_release_header/show.html.twig', [
            'inventoryReleaseHeader' => $inventoryReleaseHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_stock_inventory_release_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_MATERIAL_RELEASE_EDIT')]
    public function edit(Request $request, InventoryReleaseHeader $inventoryReleaseHeader, InventoryReleaseHeaderFormService $inventoryReleaseHeaderFormService, $_format = 'html'): Response
    {
        $inventoryReleaseHeaderFormService->initialize($inventoryReleaseHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryReleaseHeaderType::class, $inventoryReleaseHeader);
        $form->handleRequest($request);
        $inventoryReleaseHeaderFormService->finalize($inventoryReleaseHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $inventoryReleaseHeaderFormService->save($inventoryReleaseHeader);

            return $this->redirectToRoute('app_stock_inventory_release_header_show', ['id' => $inventoryReleaseHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_release_header/edit.{$_format}.twig", [
            'inventoryReleaseHeader' => $inventoryReleaseHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_inventory_release_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_MATERIAL_RELEASE_EDIT')]
    public function delete(Request $request, InventoryReleaseHeader $inventoryReleaseHeader, InventoryReleaseHeaderRepository $inventoryReleaseHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inventoryReleaseHeader->getId(), $request->request->get('_token'))) {
            $inventoryReleaseHeaderRepository->remove($inventoryReleaseHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_inventory_release_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
