<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\SalePaymentHeader;
use App\Form\Transaction\SalePaymentHeaderType;
use App\Grid\Transaction\SalePaymentHeaderGridType;
use App\Repository\Transaction\SalePaymentHeaderRepository;
use App\Service\Transaction\SalePaymentHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/sale_payment_header')]
class SalePaymentHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_sale_payment_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, SalePaymentHeaderRepository $salePaymentHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(SalePaymentHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $salePaymentHeaders) = $salePaymentHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->addOrderBy("{$alias}.transactionDate", 'DESC');
            if (isset($request->query->get('sale_payment_header_grid')['filter']['customer:company']) && isset($request->query->get('sale_payment_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->query->get('sale_payment_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->query->get('sale_payment_header_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("transaction/sale_payment_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'salePaymentHeaders' => $salePaymentHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_sale_payment_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/sale_payment_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_transaction_sale_payment_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, SalePaymentHeaderFormService $salePaymentHeaderFormService, $_format = 'html'): Response
    {
        $salePaymentHeader = new SalePaymentHeader();
        $salePaymentHeaderFormService->initialize($salePaymentHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SalePaymentHeaderType::class, $salePaymentHeader);
        $form->handleRequest($request);
        $salePaymentHeaderFormService->finalize($salePaymentHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $salePaymentHeaderFormService->save($salePaymentHeader);

            return $this->redirectToRoute('app_transaction_sale_payment_header_show', ['id' => $salePaymentHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/sale_payment_header/new.{$_format}.twig", [
            'salePaymentHeader' => $salePaymentHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_sale_payment_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(SalePaymentHeader $salePaymentHeader): Response
    {
        return $this->render('transaction/sale_payment_header/show.html.twig', [
            'salePaymentHeader' => $salePaymentHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_sale_payment_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, SalePaymentHeader $salePaymentHeader, SalePaymentHeaderFormService $salePaymentHeaderFormService, $_format = 'html'): Response
    {
        $salePaymentHeaderFormService->initialize($salePaymentHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SalePaymentHeaderType::class, $salePaymentHeader);
        $form->handleRequest($request);
        $salePaymentHeaderFormService->finalize($salePaymentHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $salePaymentHeaderFormService->save($salePaymentHeader);

            return $this->redirectToRoute('app_transaction_sale_payment_header_show', ['id' => $salePaymentHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/sale_payment_header/edit.{$_format}.twig", [
            'salePaymentHeader' => $salePaymentHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_sale_payment_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, SalePaymentHeader $salePaymentHeader, SalePaymentHeaderRepository $salePaymentHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $salePaymentHeader->getId(), $request->request->get('_token'))) {
            $salePaymentHeaderRepository->remove($salePaymentHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_sale_payment_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
