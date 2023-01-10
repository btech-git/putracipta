<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\PurchaseOrderHeader;
use App\Form\Transaction\PurchaseOrderHeaderPaperType;
use App\Grid\Transaction\PurchaseOrderHeaderGridType;
use App\Repository\Transaction\PurchaseOrderHeaderRepository;
use App\Service\Transaction\PurchaseOrderHeaderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/purchase_order_paper')]
class PurchaseOrderPaperController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_purchase_order_paper__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseOrderHeaderRepository $purchaseOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PurchaseOrderHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $purchaseOrderHeaders) = $purchaseOrderHeaderRepository->fetchData($criteria);

        return $this->renderForm("transaction/purchase_order_paper/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseOrderHeaders' => $purchaseOrderHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_purchase_order_paper_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/purchase_order_paper/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_transaction_purchase_order_paper_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseOrderHeaderFormService $purchaseOrderHeaderFormService, $_format = 'html'): Response
    {
        $purchaseOrderHeader = new PurchaseOrderHeader();
        $purchaseOrderHeaderFormService->initialize($purchaseOrderHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderHeaderPaperType::class, $purchaseOrderHeader);
        $form->handleRequest($request);
        $purchaseOrderHeaderFormService->finalize($purchaseOrderHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderFormService->save($purchaseOrderHeader);

            return $this->redirectToRoute('app_transaction_purchase_order_paper_show', ['id' => $purchaseOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_order_paper/new.{$_format}.twig", [
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_purchase_order_paper_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseOrderHeader $purchaseOrderHeader): Response
    {
        return $this->render('transaction/purchase_order_paper/show.html.twig', [
            'purchaseOrderHeader' => $purchaseOrderHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_purchase_order_paper_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseOrderHeader $purchaseOrderHeader, PurchaseOrderHeaderFormService $purchaseOrderHeaderFormService, $_format = 'html'): Response
    {
        $purchaseOrderHeaderFormService->initialize($purchaseOrderHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseOrderHeaderPaperType::class, $purchaseOrderHeader);
        $form->handleRequest($request);
        $purchaseOrderHeaderFormService->finalize($purchaseOrderHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $purchaseOrderHeaderFormService->save($purchaseOrderHeader);

            return $this->redirectToRoute('app_transaction_purchase_order_paper_show', ['id' => $purchaseOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/purchase_order_paper/edit.{$_format}.twig", [
            'purchaseOrderHeader' => $purchaseOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/memo', name: 'app_transaction_purchase_order_header_memo', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memo(PurchaseOrderHeader $purchaseOrderHeader): Response
    {
        $fileName = 'purchase-order.pdf';
        $htmlView = $this->renderView('transaction/purchase_order_header/memo.html.twig', [
            'purchaseOrderHeader' => $purchaseOrderHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/assets/styles/', 'memo.css');
        $pdfGenerator->generate($htmlView, $fileName);
    }
}
