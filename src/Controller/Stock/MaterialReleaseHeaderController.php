<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Stock\MaterialReleaseHeader;
use App\Form\Stock\MaterialReleaseHeaderType;
use App\Grid\Stock\MaterialReleaseHeaderGridType;
use App\Repository\Stock\MaterialReleaseHeaderRepository;
use App\Service\Stock\MaterialReleaseHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/material_release_header')]
class MaterialReleaseHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_material_release_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MaterialReleaseHeaderRepository $materialReleaseHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(MaterialReleaseHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $materialReleaseHeaders) = $materialReleaseHeaderRepository->fetchData($criteria);

        return $this->renderForm("stock/material_release_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'materialReleaseHeaders' => $materialReleaseHeaders,
        ]);
    }

    #[Route('/', name: 'app_stock_material_release_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("stock/material_release_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_stock_material_release_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, MaterialReleaseHeaderFormService $materialReleaseHeaderFormService, $_format = 'html'): Response
    {
        $materialReleaseHeader = new MaterialReleaseHeader();
        $materialReleaseHeaderFormService->initialize($materialReleaseHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(MaterialReleaseHeaderType::class, $materialReleaseHeader);
        $form->handleRequest($request);
        $materialReleaseHeaderFormService->finalize($materialReleaseHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $materialReleaseHeaderFormService->save($materialReleaseHeader);

            return $this->redirectToRoute('app_stock_material_release_header_show', ['id' => $materialReleaseHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/material_release_header/new.{$_format}.twig", [
            'materialReleaseHeader' => $materialReleaseHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_material_release_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(MaterialReleaseHeader $materialReleaseHeader): Response
    {
        return $this->render('stock/material_release_header/show.html.twig', [
            'materialReleaseHeader' => $materialReleaseHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_stock_material_release_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, MaterialReleaseHeader $materialReleaseHeader, MaterialReleaseHeaderFormService $materialReleaseHeaderFormService, $_format = 'html'): Response
    {
        $materialReleaseHeaderFormService->initialize($materialReleaseHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(MaterialReleaseHeaderType::class, $materialReleaseHeader);
        $form->handleRequest($request);
        $materialReleaseHeaderFormService->finalize($materialReleaseHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $materialReleaseHeaderFormService->save($materialReleaseHeader);

            return $this->redirectToRoute('app_stock_material_release_header_show', ['id' => $materialReleaseHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("stock/material_release_header/edit.{$_format}.twig", [
            'materialReleaseHeader' => $materialReleaseHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_material_release_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, MaterialReleaseHeader $materialReleaseHeader, MaterialReleaseHeaderRepository $materialReleaseHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $materialReleaseHeader->getId(), $request->request->get('_token'))) {
            $materialReleaseHeaderRepository->remove($materialReleaseHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_material_release_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
