<?php

namespace App\Controller\Accounting;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Accounting\ExpenseDetail;
use App\Form\Accounting\ExpenseDetailType;
use App\Grid\Accounting\ExpenseDetailGridType;
use App\Repository\Accounting\ExpenseDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accounting/expense_detail')]
class ExpenseDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_accounting_expense_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ExpenseDetailRepository $expenseDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ExpenseDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $expenseDetails) = $expenseDetailRepository->fetchData($criteria);

        return $this->renderForm("accounting/expense_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'expenseDetails' => $expenseDetails,
        ]);
    }

    #[Route('/', name: 'app_accounting_expense_detail_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("accounting/expense_detail/index.html.twig");
    }

    #[Route('/new', name: 'app_accounting_expense_detail_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, ExpenseDetailRepository $expenseDetailRepository): Response
    {
        $expenseDetail = new ExpenseDetail();
        $form = $this->createForm(ExpenseDetailType::class, $expenseDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expenseDetailRepository->add($expenseDetail, true);

            return $this->redirectToRoute('app_accounting_expense_detail_show', ['id' => $expenseDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/expense_detail/new.html.twig', [
            'expenseDetail' => $expenseDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_accounting_expense_detail_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(ExpenseDetail $expenseDetail): Response
    {
        return $this->render('accounting/expense_detail/show.html.twig', [
            'expenseDetail' => $expenseDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_accounting_expense_detail_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, ExpenseDetail $expenseDetail, ExpenseDetailRepository $expenseDetailRepository): Response
    {
        $form = $this->createForm(ExpenseDetailType::class, $expenseDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expenseDetailRepository->add($expenseDetail, true);

            return $this->redirectToRoute('app_accounting_expense_detail_show', ['id' => $expenseDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/expense_detail/edit.html.twig', [
            'expenseDetail' => $expenseDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_accounting_expense_detail_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, ExpenseDetail $expenseDetail, ExpenseDetailRepository $expenseDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $expenseDetail->getId(), $request->request->get('_token'))) {
            $expenseDetailRepository->remove($expenseDetail, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_accounting_expense_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
