<?php

namespace App\Controller\Accounting;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Accounting\ExpenseHeader;
use App\Form\Accounting\ExpenseHeaderType;
use App\Grid\Accounting\ExpenseHeaderGridType;
use App\Repository\Accounting\ExpenseHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accounting/expense_header')]
class ExpenseHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_accounting_expense_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ExpenseHeaderRepository $expenseHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ExpenseHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $expenseHeaders) = $expenseHeaderRepository->fetchData($criteria);

        return $this->renderForm("accounting/expense_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'expenseHeaders' => $expenseHeaders,
        ]);
    }

    #[Route('/', name: 'app_accounting_expense_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("accounting/expense_header/index.html.twig");
    }

    #[Route('/new', name: 'app_accounting_expense_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, ExpenseHeaderRepository $expenseHeaderRepository): Response
    {
        $expenseHeader = new ExpenseHeader();
        $form = $this->createForm(ExpenseHeaderType::class, $expenseHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expenseHeaderRepository->add($expenseHeader, true);

            return $this->redirectToRoute('app_accounting_expense_header_show', ['id' => $expenseHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/expense_header/new.html.twig', [
            'expenseHeader' => $expenseHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_accounting_expense_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(ExpenseHeader $expenseHeader): Response
    {
        return $this->render('accounting/expense_header/show.html.twig', [
            'expenseHeader' => $expenseHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_accounting_expense_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, ExpenseHeader $expenseHeader, ExpenseHeaderRepository $expenseHeaderRepository): Response
    {
        $form = $this->createForm(ExpenseHeaderType::class, $expenseHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $expenseHeaderRepository->add($expenseHeader, true);

            return $this->redirectToRoute('app_accounting_expense_header_show', ['id' => $expenseHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/expense_header/edit.html.twig', [
            'expenseHeader' => $expenseHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_accounting_expense_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, ExpenseHeader $expenseHeader, ExpenseHeaderRepository $expenseHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $expenseHeader->getId(), $request->request->get('_token'))) {
            $expenseHeaderRepository->remove($expenseHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_accounting_expense_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
