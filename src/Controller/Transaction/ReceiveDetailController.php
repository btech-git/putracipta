<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\ReceiveDetail;
use App\Form\Transaction\ReceiveDetailType;
use App\Grid\Transaction\ReceiveDetailGridType;
use App\Repository\Transaction\ReceiveDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/receive_detail')]
class ReceiveDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_receive_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ReceiveDetailRepository $receiveDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ReceiveDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $receiveDetails) = $receiveDetailRepository->fetchData($criteria);

        return $this->renderForm("transaction/receive_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'receiveDetails' => $receiveDetails,
        ]);
    }

    #[Route('/', name: 'app_transaction_receive_detail_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/receive_detail/index.html.twig");
    }

    #[Route('/new', name: 'app_transaction_receive_detail_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, ReceiveDetailRepository $receiveDetailRepository): Response
    {
        $receiveDetail = new ReceiveDetail();
        $form = $this->createForm(ReceiveDetailType::class, $receiveDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $receiveDetailRepository->add($receiveDetail, true);

            return $this->redirectToRoute('app_transaction_receive_detail_show', ['id' => $receiveDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/receive_detail/new.html.twig', [
            'receiveDetail' => $receiveDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_receive_detail_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(ReceiveDetail $receiveDetail): Response
    {
        return $this->render('transaction/receive_detail/show.html.twig', [
            'receiveDetail' => $receiveDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_receive_detail_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, ReceiveDetail $receiveDetail, ReceiveDetailRepository $receiveDetailRepository): Response
    {
        $form = $this->createForm(ReceiveDetailType::class, $receiveDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $receiveDetailRepository->add($receiveDetail, true);

            return $this->redirectToRoute('app_transaction_receive_detail_show', ['id' => $receiveDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/receive_detail/edit.html.twig', [
            'receiveDetail' => $receiveDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_receive_detail_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, ReceiveDetail $receiveDetail, ReceiveDetailRepository $receiveDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $receiveDetail->getId(), $request->request->get('_token'))) {
            $receiveDetailRepository->remove($receiveDetail, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_receive_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
