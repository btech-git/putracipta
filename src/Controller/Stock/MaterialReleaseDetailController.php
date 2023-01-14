<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Stock\MaterialReleaseDetail;
use App\Form\Stock\MaterialReleaseDetailType;
use App\Grid\Stock\MaterialReleaseDetailGridType;
use App\Repository\Stock\MaterialReleaseDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/material_release_detail')]
class MaterialReleaseDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_material_release_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MaterialReleaseDetailRepository $materialReleaseDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(MaterialReleaseDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $materialReleaseDetails) = $materialReleaseDetailRepository->fetchData($criteria);

        return $this->renderForm("stock/material_release_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'materialReleaseDetails' => $materialReleaseDetails,
        ]);
    }

    #[Route('/', name: 'app_stock_material_release_detail_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("stock/material_release_detail/index.html.twig");
    }

    #[Route('/new', name: 'app_stock_material_release_detail_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, MaterialReleaseDetailRepository $materialReleaseDetailRepository): Response
    {
        $materialReleaseDetail = new MaterialReleaseDetail();
        $form = $this->createForm(MaterialReleaseDetailType::class, $materialReleaseDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $materialReleaseDetailRepository->add($materialReleaseDetail, true);

            return $this->redirectToRoute('app_stock_material_release_detail_show', ['id' => $materialReleaseDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/material_release_detail/new.html.twig', [
            'materialReleaseDetail' => $materialReleaseDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_material_release_detail_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(MaterialReleaseDetail $materialReleaseDetail): Response
    {
        return $this->render('stock/material_release_detail/show.html.twig', [
            'materialReleaseDetail' => $materialReleaseDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_material_release_detail_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, MaterialReleaseDetail $materialReleaseDetail, MaterialReleaseDetailRepository $materialReleaseDetailRepository): Response
    {
        $form = $this->createForm(MaterialReleaseDetailType::class, $materialReleaseDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $materialReleaseDetailRepository->add($materialReleaseDetail, true);

            return $this->redirectToRoute('app_stock_material_release_detail_show', ['id' => $materialReleaseDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/material_release_detail/edit.html.twig', [
            'materialReleaseDetail' => $materialReleaseDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_material_release_detail_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, MaterialReleaseDetail $materialReleaseDetail, MaterialReleaseDetailRepository $materialReleaseDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $materialReleaseDetail->getId(), $request->request->get('_token'))) {
            $materialReleaseDetailRepository->remove($materialReleaseDetail, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_material_release_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
