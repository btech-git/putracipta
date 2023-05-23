<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
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
    #[Route('/_list', name: 'app_stock_inventory_release_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, InventoryReleaseHeaderRepository $inventoryReleaseHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(InventoryReleaseHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $inventoryReleaseHeaders) = $inventoryReleaseHeaderRepository->fetchData($criteria);

        return $this->renderForm("stock/inventory_release_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryReleaseHeaders' => $inventoryReleaseHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_inventory_release_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("stock/inventory_release_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_stock_inventory_release_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, InventoryReleaseHeaderFormService $inventoryReleaseHeaderFormService, $_format = 'html'): Response
    {
        $inventoryReleaseHeader = new InventoryReleaseHeader();
        $inventoryReleaseHeaderFormService->initialize($inventoryReleaseHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryReleaseHeaderType::class, $inventoryReleaseHeader);
        $form->handleRequest($request);
        $inventoryReleaseHeaderFormService->finalize($inventoryReleaseHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $inventoryReleaseHeaderFormService->save($inventoryReleaseHeader);

            return $this->redirectToRoute('app_stock_inventory_release_header_show', ['id' => $inventoryReleaseHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_release_header/new.{$_format}.twig", [
            'inventoryReleaseHeader' => $inventoryReleaseHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_inventory_release_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(InventoryReleaseHeader $inventoryReleaseHeader): Response
    {
        return $this->render('stock/inventory_release_header/show.html.twig', [
            'inventoryReleaseHeader' => $inventoryReleaseHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_inventory_release_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, InventoryReleaseHeader $inventoryReleaseHeader, InventoryReleaseHeaderFormService $inventoryReleaseHeaderFormService, $_format = 'html'): Response
    {
        $inventoryReleaseHeaderFormService->initialize($inventoryReleaseHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryReleaseHeaderType::class, $inventoryReleaseHeader);
        $form->handleRequest($request);
        $inventoryReleaseHeaderFormService->finalize($inventoryReleaseHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $inventoryReleaseHeaderFormService->save($inventoryReleaseHeader);

            return $this->redirectToRoute('app_stock_inventory_release_header_show', ['id' => $inventoryReleaseHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_release_header/edit.{$_format}.twig", [
            'inventoryReleaseHeader' => $inventoryReleaseHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_inventory_release_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
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