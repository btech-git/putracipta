<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Transaction\ReceiveHeader;
use App\Form\Transaction\ReceiveHeaderType;
use App\Grid\Transaction\ReceiveHeaderGridType;
use App\Repository\Transaction\ReceiveHeaderRepository;
use App\Service\Transaction\ReceiveHeaderFormService;
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
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(ReceiveHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $receiveHeaders) = $receiveHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->query->get('receive_header_grid')['filter']['supplier:company']) && isset($request->query->get('receive_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->query->get('receive_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->query->get('receive_header_grid')['sort']['supplier:company']);
            }
            if (isset($request->query->get('receive_header_grid')['filter']['warehouse:name']) && isset($request->query->get('receive_header_grid')['sort']['warehouse:name'])) {
                $qb->innerJoin("{$alias}.warehouse", 'w');
                $add['filter']($qb, 'w', 'name', $request->query->get('receive_header_grid')['filter']['warehouse:name']);
                $add['sort']($qb, 'w', 'name', $request->query->get('receive_header_grid')['sort']['warehouse:name']);
            }
        });

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

    #[Route('/new.{_format}', name: 'app_transaction_receive_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, ReceiveHeaderFormService $receiveHeaderFormService, $_format = 'html'): Response
    {
        $receiveHeader = new ReceiveHeader();
        $receiveHeaderFormService->initialize($receiveHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader);
        $form->handleRequest($request);
        $receiveHeaderFormService->finalize($receiveHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $receiveHeaderFormService->save($receiveHeader);

            return $this->redirectToRoute('app_transaction_receive_header_show', ['id' => $receiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/receive_header/new.{$_format}.twig", [
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

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_receive_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, ReceiveHeader $receiveHeader, ReceiveHeaderFormService $receiveHeaderFormService, $_format = 'html'): Response
    {
        $receiveHeaderFormService->initialize($receiveHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader);
        $form->handleRequest($request);
        $receiveHeaderFormService->finalize($receiveHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $receiveHeaderFormService->save($receiveHeader);

            return $this->redirectToRoute('app_transaction_receive_header_show', ['id' => $receiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/receive_header/edit.{$_format}.twig", [
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
