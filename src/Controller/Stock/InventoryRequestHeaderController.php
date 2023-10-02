<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Stock\InventoryRequestHeader;
use App\Form\Stock\InventoryRequestHeaderType;
use App\Grid\Stock\InventoryRequestHeaderGridType;
use App\Repository\Stock\InventoryRequestHeaderRepository;
use App\Service\Stock\InventoryRequestHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/inventory_request_header')]
class InventoryRequestHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_inventory_request_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, InventoryRequestHeaderRepository $inventoryRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(InventoryRequestHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $inventoryRequestHeaders) = $inventoryRequestHeaderRepository->fetchData($criteria);

        return $this->renderForm("stock/inventory_request_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryRequestHeaders' => $inventoryRequestHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_inventory_request_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("stock/inventory_request_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_stock_inventory_request_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, InventoryRequestHeaderFormService $inventoryRequestHeaderFormService, $_format = 'html'): Response
    {
        $inventoryRequestHeader = new InventoryRequestHeader();
        $inventoryRequestHeaderFormService->initialize($inventoryRequestHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryRequestHeaderType::class, $inventoryRequestHeader);
        $form->handleRequest($request);
        $inventoryRequestHeaderFormService->finalize($inventoryRequestHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $inventoryRequestHeaderFormService->save($inventoryRequestHeader);

            return $this->redirectToRoute('app_stock_inventory_request_header_show', ['id' => $inventoryRequestHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_request_header/new.{$_format}.twig", [
            'inventoryRequestHeader' => $inventoryRequestHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_inventory_request_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(InventoryRequestHeader $inventoryRequestHeader): Response
    {
        return $this->render('stock/inventory_request_header/show.html.twig', [
            'inventoryRequestHeader' => $inventoryRequestHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_inventory_request_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, InventoryRequestHeader $inventoryRequestHeader, InventoryRequestHeaderFormService $inventoryRequestHeaderFormService, $_format = 'html'): Response
    {
        $inventoryRequestHeaderFormService->initialize($inventoryRequestHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryRequestHeaderType::class, $inventoryRequestHeader);
        $form->handleRequest($request);
        $inventoryRequestHeaderFormService->finalize($inventoryRequestHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $inventoryRequestHeaderFormService->save($inventoryRequestHeader);

            return $this->redirectToRoute('app_stock_inventory_request_header_show', ['id' => $inventoryRequestHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_request_header/edit.{$_format}.twig", [
            'inventoryRequestHeader' => $inventoryRequestHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_inventory_request_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, InventoryRequestHeader $inventoryRequestHeader, InventoryRequestHeaderRepository $inventoryRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inventoryRequestHeader->getId(), $request->request->get('_token'))) {
            $inventoryRequestHeaderRepository->remove($inventoryRequestHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_inventory_request_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
