<?php

namespace App\Controller\Accounting;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Accounting\JournalVoucherHeader;
use App\Form\Accounting\JournalVoucherHeaderType;
use App\Grid\Accounting\JournalVoucherHeaderGridType;
use App\Repository\Accounting\JournalVoucherHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accounting/journal_voucher_header')]
class JournalVoucherHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_accounting_journal_voucher_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, JournalVoucherHeaderRepository $journalVoucherHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(JournalVoucherHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $journalVoucherHeaders) = $journalVoucherHeaderRepository->fetchData($criteria);

        return $this->renderForm("accounting/journal_voucher_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'journalVoucherHeaders' => $journalVoucherHeaders,
        ]);
    }

    #[Route('/', name: 'app_accounting_journal_voucher_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("accounting/journal_voucher_header/index.html.twig");
    }

    #[Route('/new', name: 'app_accounting_journal_voucher_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, JournalVoucherHeaderRepository $journalVoucherHeaderRepository): Response
    {
        $journalVoucherHeader = new JournalVoucherHeader();
        $form = $this->createForm(JournalVoucherHeaderType::class, $journalVoucherHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $journalVoucherHeaderRepository->add($journalVoucherHeader, true);

            return $this->redirectToRoute('app_accounting_journal_voucher_header_show', ['id' => $journalVoucherHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/journal_voucher_header/new.html.twig', [
            'journalVoucherHeader' => $journalVoucherHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_accounting_journal_voucher_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(JournalVoucherHeader $journalVoucherHeader): Response
    {
        return $this->render('accounting/journal_voucher_header/show.html.twig', [
            'journalVoucherHeader' => $journalVoucherHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_accounting_journal_voucher_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, JournalVoucherHeader $journalVoucherHeader, JournalVoucherHeaderRepository $journalVoucherHeaderRepository): Response
    {
        $form = $this->createForm(JournalVoucherHeaderType::class, $journalVoucherHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $journalVoucherHeaderRepository->add($journalVoucherHeader, true);

            return $this->redirectToRoute('app_accounting_journal_voucher_header_show', ['id' => $journalVoucherHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/journal_voucher_header/edit.html.twig', [
            'journalVoucherHeader' => $journalVoucherHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_accounting_journal_voucher_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, JournalVoucherHeader $journalVoucherHeader, JournalVoucherHeaderRepository $journalVoucherHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $journalVoucherHeader->getId(), $request->request->get('_token'))) {
            $journalVoucherHeaderRepository->remove($journalVoucherHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_accounting_journal_voucher_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
