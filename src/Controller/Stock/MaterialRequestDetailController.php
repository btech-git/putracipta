<?php

namespace App\Controller\Stock;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Stock\MaterialRequestDetail;
use App\Form\Stock\MaterialRequestDetailType;
use App\Grid\Stock\MaterialRequestDetailGridType;
use App\Repository\Stock\MaterialRequestDetailRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/stock/material_request_detail')]
class MaterialRequestDetailController extends AbstractController
{
    #[Route('/_list', name: 'app_stock_material_request_detail__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MaterialRequestDetailRepository $materialRequestDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(MaterialRequestDetailGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $materialRequestDetails) = $materialRequestDetailRepository->fetchData($criteria);

        return $this->renderForm("stock/material_request_detail/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'materialRequestDetails' => $materialRequestDetails,
        ]);
    }

    #[Route('/', name: 'app_stock_material_request_detail_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("stock/material_request_detail/index.html.twig");
    }

    #[Route('/new', name: 'app_stock_material_request_detail_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, MaterialRequestDetailRepository $materialRequestDetailRepository): Response
    {
        $materialRequestDetail = new MaterialRequestDetail();
        $form = $this->createForm(MaterialRequestDetailType::class, $materialRequestDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $materialRequestDetailRepository->add($materialRequestDetail, true);

            return $this->redirectToRoute('app_stock_material_request_detail_show', ['id' => $materialRequestDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/material_request_detail/new.html.twig', [
            'materialRequestDetail' => $materialRequestDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_stock_material_request_detail_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(MaterialRequestDetail $materialRequestDetail): Response
    {
        return $this->render('stock/material_request_detail/show.html.twig', [
            'materialRequestDetail' => $materialRequestDetail,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_stock_material_request_detail_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, MaterialRequestDetail $materialRequestDetail, MaterialRequestDetailRepository $materialRequestDetailRepository): Response
    {
        $form = $this->createForm(MaterialRequestDetailType::class, $materialRequestDetail);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $materialRequestDetailRepository->add($materialRequestDetail, true);

            return $this->redirectToRoute('app_stock_material_request_detail_show', ['id' => $materialRequestDetail->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('stock/material_request_detail/edit.html.twig', [
            'materialRequestDetail' => $materialRequestDetail,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_stock_material_request_detail_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, MaterialRequestDetail $materialRequestDetail, MaterialRequestDetailRepository $materialRequestDetailRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $materialRequestDetail->getId(), $request->request->get('_token'))) {
            $materialRequestDetailRepository->remove($materialRequestDetail, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_stock_material_request_detail_index', [], Response::HTTP_SEE_OTHER);
    }
}
