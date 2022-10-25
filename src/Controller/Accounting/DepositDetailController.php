<?php

namespace App\Controller\Accounting;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Accounting\DepositDetail;
use App\Form\Accounting\DepositDetailType;
use App\Grid\Accounting\DepositDetailGridType;
use App\Repository\Accounting\DepositDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/accounting/deposit_detail')]
class DepositDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_accounting_deposit_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DepositDetailRepository $depositDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(DepositDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $depositDetails) = $depositDetailRepository->fetchData($criteria);

        return $this->renderForm("accounting/deposit_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'depositDetails' => $depositDetails,
        ]);
    }

    #[Route('/', name: 'app_accounting_deposit_detail_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("accounting/deposit_detail/index.html.twig");
    }

    #[Route('/new', name: 'app_accounting_deposit_detail_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, DepositDetailRepository $depositDetailRepository): Response
    {
        $depositDetail = new DepositDetail();
        $form = $this->createForm(DepositDetailType::class, $depositDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depositDetailRepository->add($depositDetail, true);

            return $this->redirectToRoute('app_accounting_deposit_detail_show', ['id' => $depositDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/deposit_detail/new.html.twig', [
            'depositDetail' => $depositDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_accounting_deposit_detail_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(DepositDetail $depositDetail): Response
    {
        return $this->render('accounting/deposit_detail/show.html.twig', [
            'depositDetail' => $depositDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_accounting_deposit_detail_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, DepositDetail $depositDetail, DepositDetailRepository $depositDetailRepository): Response
    {
        $form = $this->createForm(DepositDetailType::class, $depositDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $depositDetailRepository->add($depositDetail, true);

            return $this->redirectToRoute('app_accounting_deposit_detail_show', ['id' => $depositDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('accounting/deposit_detail/edit.html.twig', [
            'depositDetail' => $depositDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_accounting_deposit_detail_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, DepositDetail $depositDetail, DepositDetailRepository $depositDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $depositDetail->getId(), $request->request->get('_token'))) {
            $depositDetailRepository->remove($depositDetail, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_accounting_deposit_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
