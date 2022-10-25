<?php

namespace App\Controller\Accounting;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Accounting\JournalVoucherDetail;
use App\Form\Accounting\JournalVoucherDetailType;
use App\Grid\Accounting\JournalVoucherDetailGridType;
use App\Repository\Accounting\JournalVoucherDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accounting/journal_voucher_detail')]
class JournalVoucherDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_accounting_journal_voucher_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, JournalVoucherDetailRepository $journalVoucherDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(JournalVoucherDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $journalVoucherDetails) = $journalVoucherDetailRepository->fetchData($criteria);

        return $this->renderForm("accounting/journal_voucher_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'journalVoucherDetails' => $journalVoucherDetails,
        ]);
    }

    #[Route('/', name: 'app_accounting_journal_voucher_detail_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("accounting/journal_voucher_detail/index.html.twig");
    }

    #[Route('/new', name: 'app_accounting_journal_voucher_detail_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, JournalVoucherDetailRepository $journalVoucherDetailRepository): Response
    {
        $journalVoucherDetail = new JournalVoucherDetail();
        $form = $this->createForm(JournalVoucherDetailType::class, $journalVoucherDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $journalVoucherDetailRepository->add($journalVoucherDetail, true);

            return $this->redirectToRoute('app_accounting_journal_voucher_detail_show', ['id' => $journalVoucherDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/journal_voucher_detail/new.html.twig', [
            'journalVoucherDetail' => $journalVoucherDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_accounting_journal_voucher_detail_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(JournalVoucherDetail $journalVoucherDetail): Response
    {
        return $this->render('accounting/journal_voucher_detail/show.html.twig', [
            'journalVoucherDetail' => $journalVoucherDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_accounting_journal_voucher_detail_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, JournalVoucherDetail $journalVoucherDetail, JournalVoucherDetailRepository $journalVoucherDetailRepository): Response
    {
        $form = $this->createForm(JournalVoucherDetailType::class, $journalVoucherDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $journalVoucherDetailRepository->add($journalVoucherDetail, true);

            return $this->redirectToRoute('app_accounting_journal_voucher_detail_show', ['id' => $journalVoucherDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/journal_voucher_detail/edit.html.twig', [
            'journalVoucherDetail' => $journalVoucherDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_accounting_journal_voucher_detail_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, JournalVoucherDetail $journalVoucherDetail, JournalVoucherDetailRepository $journalVoucherDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $journalVoucherDetail->getId(), $request->request->get('_token'))) {
            $journalVoucherDetailRepository->remove($journalVoucherDetail, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_accounting_journal_voucher_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
