<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\SaleInvoiceHeader;
use App\Form\Transaction\SaleInvoiceHeaderType;
use App\Grid\Transaction\SaleInvoiceHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Transaction\SaleInvoiceHeaderRepository;
use App\Service\Transaction\SaleInvoiceHeaderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/sale_invoice_header')]
class SaleInvoiceHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_sale_invoice_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(SaleInvoiceHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $saleInvoiceHeaders) = $saleInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->query->get('sale_invoice_header_grid')['filter']['customer:company']) && isset($request->query->get('sale_invoice_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->query->get('sale_invoice_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->query->get('sale_invoice_header_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("transaction/sale_invoice_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleInvoiceHeaders' => $saleInvoiceHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_sale_invoice_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/sale_invoice_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_transaction_sale_invoice_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, SaleInvoiceHeaderFormService $saleInvoiceHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $saleInvoiceHeader = new SaleInvoiceHeader();
        $saleInvoiceHeaderFormService->initialize($saleInvoiceHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SaleInvoiceHeaderType::class, $saleInvoiceHeader);
        $form->handleRequest($request);
        $saleInvoiceHeaderFormService->finalize($saleInvoiceHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage'), 'serviceTaxPercentage' => $literalConfigRepository->findLiteralValue('serviceTaxPercentage')]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceHeaderFormService->save($saleInvoiceHeader);

            return $this->redirectToRoute('app_transaction_sale_invoice_header_show', ['id' => $saleInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/sale_invoice_header/new.{$_format}.twig", [
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_sale_invoice_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(SaleInvoiceHeader $saleInvoiceHeader): Response
    {
        return $this->render('transaction/sale_invoice_header/show.html.twig', [
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_sale_invoice_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, SaleInvoiceHeader $saleInvoiceHeader, SaleInvoiceHeaderFormService $saleInvoiceHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $saleInvoiceHeaderFormService->initialize($saleInvoiceHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SaleInvoiceHeaderType::class, $saleInvoiceHeader);
        $form->handleRequest($request);
        $saleInvoiceHeaderFormService->finalize($saleInvoiceHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage'), 'serviceTaxPercentage' => $literalConfigRepository->findLiteralValue('serviceTaxPercentage')]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleInvoiceHeaderFormService->save($saleInvoiceHeader);

            return $this->redirectToRoute('app_transaction_sale_invoice_header_show', ['id' => $saleInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/sale_invoice_header/edit.{$_format}.twig", [
            'saleInvoiceHeader' => $saleInvoiceHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_sale_invoice_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, SaleInvoiceHeader $saleInvoiceHeader, SaleInvoiceHeaderRepository $saleInvoiceHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $saleInvoiceHeader->getId(), $request->request->get('_token'))) {
            $saleInvoiceHeaderRepository->remove($saleInvoiceHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_sale_invoice_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/memo', name: 'app_transaction_sale_invoice_header_memo', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memo(SaleInvoiceHeader $saleInvoiceHeader): Response
    {
        $fileName = 'sale_invoice.pdf';
        $htmlView = $this->render('transaction/sale_invoice_header/memo.html.twig', [
            'saleInvoiceHeader' => $saleInvoiceHeader,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link(.+)href=".+">/', '<link\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img(.+)src=".+">/', '<img\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
        ]);
    }
}
