<?php

namespace App\Controller\Purchase;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Form\Type\PaginationType;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseInvoiceHeader;
use App\Form\Purchase\PurchaseInvoiceHeaderType;
use App\Grid\Purchase\PurchaseInvoiceHeaderGridType;
use App\Repository\Purchase\PurchaseInvoiceHeaderRepository;
use App\Service\Purchase\PurchaseInvoiceHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase/purchase_invoice_header')]
class PurchaseInvoiceHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_purchase_purchase_invoice_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(PurchaseInvoiceHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $purchaseInvoiceHeaders) = $purchaseInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('purchase_invoice_header_grid')['filter']['supplier:company']) && isset($request->request->get('purchase_invoice_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('purchase_invoice_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('purchase_invoice_header_grid')['sort']['supplier:company']);
            }
        });

        return $this->renderForm("purchase/purchase_invoice_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseInvoiceHeaders' => $purchaseInvoiceHeaders,
        ]);
    }

    #[Route('/', name: 'app_purchase_purchase_invoice_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("purchase/purchase_invoice_header/index.html.twig");
    }

    #[Route('/_head', name: 'app_purchase_purchase_invoice_header__head', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _head(Request $request, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createFormBuilder($criteria, ['method' => 'GET', 'data_class' => DataCriteria::class, 'csrf_protection' => false])
                ->add('pagination', PaginationType::class, ['size_choices' => [10, 20, 50, 100]])
                ->getForm();
        $form->handleRequest($request);

        list($count, $purchaseInvoiceHeaders) = $purchaseInvoiceHeaderRepository->fetchData($criteria, function($qb, $alias) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("{$alias}.isRead = false");
        });

        return $this->renderForm("purchase/purchase_invoice_header/_head.html.twig", [
            'form' => $form,
            'count' => $count,
            'purchaseInvoiceHeaders' => $purchaseInvoiceHeaders,
        ]);
    }

    #[Route('/head', name: 'app_purchase_purchase_invoice_header_head', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function head(): Response
    {
        return $this->render("purchase/purchase_invoice_header/head.html.twig");
    }

    #[Route('/{id}/read', name: 'app_purchase_purchase_invoice_header_read', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function read(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('read' . $purchaseInvoiceHeader->getId(), $request->request->get('_token'))) {
            $purchaseInvoiceHeader->setIsRead(true);
            $purchaseInvoiceHeaderRepository->add($purchaseInvoiceHeader, true);
        }

        return $this->redirectToRoute('app_purchase_purchase_invoice_header_show', ['id' => $purchaseInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/new.{_format}', name: 'app_purchase_purchase_invoice_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PurchaseInvoiceHeaderFormService $purchaseInvoiceHeaderFormService, $_format = 'html'): Response
    {
        $purchaseInvoiceHeader = new PurchaseInvoiceHeader();
        $purchaseInvoiceHeaderFormService->initialize($purchaseInvoiceHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseInvoiceHeaderType::class, $purchaseInvoiceHeader);
        $form->handleRequest($request);
        $purchaseInvoiceHeaderFormService->finalize($purchaseInvoiceHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderFormService->save($purchaseInvoiceHeader);

            return $this->redirectToRoute('app_purchase_purchase_invoice_header_show', ['id' => $purchaseInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_invoice_header/new.{$_format}.twig", [
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_purchase_invoice_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PurchaseInvoiceHeader $purchaseInvoiceHeader): Response
    {
        return $this->render('purchase/purchase_invoice_header/show.html.twig', [
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_purchase_purchase_invoice_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader, PurchaseInvoiceHeaderFormService $purchaseInvoiceHeaderFormService, $_format = 'html'): Response
    {
        $purchaseInvoiceHeaderFormService->initialize($purchaseInvoiceHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(PurchaseInvoiceHeaderType::class, $purchaseInvoiceHeader);
        $form->handleRequest($request);
        $purchaseInvoiceHeaderFormService->finalize($purchaseInvoiceHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $purchaseInvoiceHeaderFormService->save($purchaseInvoiceHeader);

            return $this->redirectToRoute('app_purchase_purchase_invoice_header_show', ['id' => $purchaseInvoiceHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/purchase_invoice_header/edit.{$_format}.twig", [
            'purchaseInvoiceHeader' => $purchaseInvoiceHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_purchase_purchase_invoice_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PurchaseInvoiceHeader $purchaseInvoiceHeader, PurchaseInvoiceHeaderRepository $purchaseInvoiceHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $purchaseInvoiceHeader->getId(), $request->request->get('_token'))) {
            $purchaseInvoiceHeaderRepository->remove($purchaseInvoiceHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_purchase_purchase_invoice_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
