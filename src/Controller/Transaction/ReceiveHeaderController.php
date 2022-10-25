<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\ReceiveHeader;
use App\Form\Transaction\ReceiveHeaderType;
use App\Grid\Transaction\ReceiveHeaderGridType;
use App\Repository\Transaction\ReceiveHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/receive_header')]
class ReceiveHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_receive_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, ReceiveHeaderRepository $receiveHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(ReceiveHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $receiveHeaders) = $receiveHeaderRepository->fetchData($criteria);

        return $this->renderForm("transaction/receive_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'receiveHeaders' => $receiveHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_receive_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/receive_header/index.html.twig");
    }

    #[Route('/new', name: 'app_transaction_receive_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, ReceiveHeaderRepository $receiveHeaderRepository): Response
    {
        $receiveHeader = new ReceiveHeader();
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $receiveHeaderRepository->add($receiveHeader, true);

            return $this->redirectToRoute('app_transaction_receive_header_show', ['id' => $receiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/receive_header/new.html.twig', [
            'receiveHeader' => $receiveHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_receive_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(ReceiveHeader $receiveHeader): Response
    {
        return $this->render('transaction/receive_header/show.html.twig', [
            'receiveHeader' => $receiveHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_transaction_receive_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, ReceiveHeader $receiveHeader, ReceiveHeaderRepository $receiveHeaderRepository): Response
    {
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $receiveHeaderRepository->add($receiveHeader, true);

            return $this->redirectToRoute('app_transaction_receive_header_show', ['id' => $receiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('transaction/receive_header/edit.html.twig', [
            'receiveHeader' => $receiveHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_receive_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, ReceiveHeader $receiveHeader, ReceiveHeaderRepository $receiveHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $receiveHeader->getId(), $request->request->get('_token'))) {
            $receiveHeaderRepository->remove($receiveHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_receive_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
