<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Stock\InventoryProductReceiveHeader;
use App\Form\Stock\InventoryProductReceiveHeaderType;
use App\Grid\Stock\InventoryProductReceiveHeaderGridType;
use App\Repository\Stock\InventoryProductReceiveHeaderRepository;
use App\Service\Stock\InventoryProductReceiveHeaderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/inventory_product_receive_header')]
class InventoryProductReceiveHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_inventory_product_receive_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, InventoryProductReceiveHeaderRepository $inventoryProductReceiveHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(InventoryProductReceiveHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $inventoryProductReceiveHeaders) = $inventoryProductReceiveHeaderRepository->fetchData($criteria);

        return $this->renderForm("stock/inventory_product_receive_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'inventoryProductReceiveHeaders' => $inventoryProductReceiveHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_inventory_product_receive_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("stock/inventory_product_receive_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_stock_inventory_product_receive_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, InventoryProductReceiveHeaderFormService $inventoryProductReceiveHeaderFormService, $_format = 'html'): Response
    {
        $inventoryProductReceiveHeader = new InventoryProductReceiveHeader();
        $inventoryProductReceiveHeaderFormService->initialize($inventoryProductReceiveHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryProductReceiveHeaderType::class, $inventoryProductReceiveHeader);
        $form->handleRequest($request);
        $inventoryProductReceiveHeaderFormService->finalize($inventoryProductReceiveHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $inventoryProductReceiveHeaderFormService->save($inventoryProductReceiveHeader);

            return $this->redirectToRoute('app_stock_inventory_product_receive_header_show', ['id' => $inventoryProductReceiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_product_receive_header/new.{$_format}.twig", [
            'inventoryProductReceiveHeader' => $inventoryProductReceiveHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_inventory_product_receive_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(InventoryProductReceiveHeader $inventoryProductReceiveHeader): Response
    {
        return $this->render('stock/inventory_product_receive_header/show.html.twig', [
            'inventoryProductReceiveHeader' => $inventoryProductReceiveHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_stock_inventory_product_receive_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, InventoryProductReceiveHeader $inventoryProductReceiveHeader, InventoryProductReceiveHeaderFormService $inventoryProductReceiveHeaderFormService, $_format = 'html'): Response
    {
        $inventoryProductReceiveHeaderFormService->initialize($inventoryProductReceiveHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(InventoryProductReceiveHeaderType::class, $inventoryProductReceiveHeader);
        $form->handleRequest($request);
        $inventoryProductReceiveHeaderFormService->finalize($inventoryProductReceiveHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $inventoryProductReceiveHeaderFormService->save($inventoryProductReceiveHeader);

            return $this->redirectToRoute('app_stock_inventory_product_receive_header_show', ['id' => $inventoryProductReceiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/inventory_product_receive_header/edit.{$_format}.twig", [
            'inventoryProductReceiveHeader' => $inventoryProductReceiveHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/memo_inventory_product_receive_header', name: 'app_stock_inventory_product_receive_header_memo', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memo(InventoryProductReceiveHeader $inventoryProductReceiveHeader): Response
    {
        $fileName = 'inventory_product_receive.pdf';
        $htmlView = $this->renderView('stock/inventory_product_receive_header/memo.html.twig', [
            'inventoryProductReceiveHeader' => $inventoryProductReceiveHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="logo"(.+)src=".+">/', '<img id="logo"\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
        ]);
    }
    
    #[Route('/{id}/delete', name: 'app_stock_inventory_product_receive_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, InventoryProductReceiveHeader $inventoryProductReceiveHeader, InventoryProductReceiveHeaderRepository $inventoryProductReceiveHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $inventoryProductReceiveHeader->getId(), $request->request->get('_token'))) {
            $inventoryProductReceiveHeaderRepository->remove($inventoryProductReceiveHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_inventory_product_receive_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
