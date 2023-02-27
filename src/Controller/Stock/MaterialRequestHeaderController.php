<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Stock\MaterialRequestHeader;
use App\Form\Stock\MaterialRequestHeaderType;
use App\Grid\Stock\MaterialRequestHeaderGridType;
use App\Repository\Stock\MaterialRequestHeaderRepository;
use App\Service\Stock\MaterialRequestHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/material_request_header')]
class MaterialRequestHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_material_request_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MaterialRequestHeaderRepository $materialRequestHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(MaterialRequestHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $materialRequestHeaders) = $materialRequestHeaderRepository->fetchData($criteria);

        return $this->renderForm("stock/material_request_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'materialRequestHeaders' => $materialRequestHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_material_request_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("stock/material_request_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_stock_material_request_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, MaterialRequestHeaderFormService $materialRequestHeaderFormService, $_format = 'html'): Response
    {
        $materialRequestHeader = new MaterialRequestHeader();
        $materialRequestHeaderFormService->initialize($materialRequestHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(MaterialRequestHeaderType::class, $materialRequestHeader);
        $form->handleRequest($request);
        $materialRequestHeaderFormService->finalize($materialRequestHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $materialRequestHeaderFormService->save($materialRequestHeader);

            return $this->redirectToRoute('app_stock_material_request_header_show', ['id' => $materialRequestHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/material_request_header/new.{$_format}.twig", [
            'materialRequestHeader' => $materialRequestHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_material_request_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(MaterialRequestHeader $materialRequestHeader): Response
    {
        return $this->render('stock/material_request_header/show.html.twig', [
            'materialRequestHeader' => $materialRequestHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_stock_material_request_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, MaterialRequestHeader $materialRequestHeader, MaterialRequestHeaderFormService $materialRequestHeaderFormService, $_format = 'html'): Response
    {
        $materialRequestHeaderFormService->initialize($materialRequestHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(MaterialRequestHeaderType::class, $materialRequestHeader);
        $form->handleRequest($request);
        $materialRequestHeaderFormService->finalize($materialRequestHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $materialRequestHeaderFormService->save($materialRequestHeader);

            return $this->redirectToRoute('app_stock_material_request_header_show', ['id' => $materialRequestHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/material_request_header/edit.{$_format}.twig", [
            'materialRequestHeader' => $materialRequestHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_material_request_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, MaterialRequestHeader $materialRequestHeader, MaterialRequestHeaderRepository $materialRequestHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $materialRequestHeader->getId(), $request->request->get('_token'))) {
            $materialRequestHeaderRepository->remove($materialRequestHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_material_request_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
