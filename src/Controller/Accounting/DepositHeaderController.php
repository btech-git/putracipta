<?php

namespace App\Controller\Accounting;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Accounting\DepositHeader;
use App\Form\Accounting\DepositHeaderType;
use App\Grid\Accounting\DepositHeaderGridType;
use App\Repository\Accounting\DepositHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accounting/deposit_header')]
class DepositHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_accounting_deposit_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DepositHeaderRepository $depositHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(DepositHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $depositHeaders) = $depositHeaderRepository->fetchData($criteria);

        return $this->renderForm("accounting/deposit_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'depositHeaders' => $depositHeaders,
        ]);
    }

    #[Route('/', name: 'app_accounting_deposit_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("accounting/deposit_header/index.html.twig");
    }

    #[Route('/new', name: 'app_accounting_deposit_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, DepositHeaderRepository $depositHeaderRepository): Response
    {
        $depositHeader = new DepositHeader();
        $form = $this->createForm(DepositHeaderType::class, $depositHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depositHeaderRepository->add($depositHeader, true);

            return $this->redirectToRoute('app_accounting_deposit_header_show', ['id' => $depositHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/deposit_header/new.html.twig', [
            'depositHeader' => $depositHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_accounting_deposit_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(DepositHeader $depositHeader): Response
    {
        return $this->render('accounting/deposit_header/show.html.twig', [
            'depositHeader' => $depositHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_accounting_deposit_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, DepositHeader $depositHeader, DepositHeaderRepository $depositHeaderRepository): Response
    {
        $form = $this->createForm(DepositHeaderType::class, $depositHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depositHeaderRepository->add($depositHeader, true);

            return $this->redirectToRoute('app_accounting_deposit_header_show', ['id' => $depositHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/deposit_header/edit.html.twig', [
            'depositHeader' => $depositHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_accounting_deposit_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, DepositHeader $depositHeader, DepositHeaderRepository $depositHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $depositHeader->getId(), $request->request->get('_token'))) {
            $depositHeaderRepository->remove($depositHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_accounting_deposit_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
