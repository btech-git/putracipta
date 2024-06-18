<?php

namespace App\Controller\Purchase;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\ReceiveHeader;
use App\Form\Purchase\ReceiveHeaderType;
use App\Grid\Purchase\ReceiveHeaderGridType;
use App\Repository\Purchase\ReceiveHeaderRepository;
use App\Service\Purchase\ReceiveHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/purchase/receive_header')]
class ReceiveHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_purchase_receive_header__list', methods: ['GET', 'POST'])]
    #[Security("is_granted('ROLE_RECEIVE_ADD') or is_granted('ROLE_RECEIVE_EDIT') or is_granted('ROLE_RECEIVE_VIEW')")]
    public function _list(Request $request, ReceiveHeaderRepository $receiveHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(ReceiveHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $receiveHeaders) = $receiveHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('receive_header_grid')['filter']['supplier:company']) && isset($request->request->get('receive_header_grid')['sort']['supplier:company'])) {
                $qb->innerJoin("{$alias}.supplier", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('receive_header_grid')['filter']['supplier:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('receive_header_grid')['sort']['supplier:company']);
            }
//            if (isset($request->request->get('receive_header_grid')['filter']['warehouse:name']) && isset($request->request->get('receive_header_grid')['sort']['warehouse:name'])) {
//                $qb->innerJoin("{$alias}.warehouse", 'w');
//                $add['filter']($qb, 'w', 'name', $request->request->get('receive_header_grid')['filter']['warehouse:name']);
//                $add['sort']($qb, 'w', 'name', $request->request->get('receive_header_grid')['sort']['warehouse:name']);
//            }
        });

        return $this->renderForm("purchase/receive_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'receiveHeaders' => $receiveHeaders,
        ]);
    }

    #[Route('/', name: 'app_purchase_receive_header_index', methods: ['GET'])]
    #[Security("is_granted('ROLE_RECEIVE_ADD') or is_granted('ROLE_RECEIVE_EDIT') or is_granted('ROLE_RECEIVE_VIEW')")]
    public function index(): Response
    {
        return $this->render("purchase/receive_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_purchase_receive_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RECEIVE_ADD')]
    public function new(Request $request, ReceiveHeaderFormService $receiveHeaderFormService, $_format = 'html'): Response
    {
        $receiveHeader = new ReceiveHeader();
        $receiveHeaderFormService->initialize($receiveHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader);
        $form->handleRequest($request);
        $receiveHeaderFormService->finalize($receiveHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $receiveHeaderFormService->save($receiveHeader);

            return $this->redirectToRoute('app_purchase_receive_header_show', ['id' => $receiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/receive_header/new.{$_format}.twig", [
            'receiveHeader' => $receiveHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_purchase_receive_header_show', methods: ['GET'])]
    #[Security("is_granted('ROLE_RECEIVE_ADD') or is_granted('ROLE_RECEIVE_EDIT') or is_granted('ROLE_RECEIVE_VIEW')")]
    public function show(ReceiveHeader $receiveHeader): Response
    {
        return $this->render('purchase/receive_header/show.html.twig', [
            'receiveHeader' => $receiveHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_purchase_receive_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_RECEIVE_EDIT')]
    public function edit(Request $request, ReceiveHeader $receiveHeader, ReceiveHeaderFormService $receiveHeaderFormService, $_format = 'html'): Response
    {
        $receiveHeaderFormService->initialize($receiveHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(ReceiveHeaderType::class, $receiveHeader);
        $form->handleRequest($request);
        $receiveHeaderFormService->finalize($receiveHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $receiveHeaderFormService->save($receiveHeader);

            return $this->redirectToRoute('app_purchase_receive_header_show', ['id' => $receiveHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("purchase/receive_header/edit.{$_format}.twig", [
            'receiveHeader' => $receiveHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_purchase_receive_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_RECEIVE_EDIT')]
    public function delete(Request $request, ReceiveHeader $receiveHeader, ReceiveHeaderRepository $receiveHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $receiveHeader->getId(), $request->request->get('_token'))) {
            $receiveHeaderRepository->remove($receiveHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_purchase_receive_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
