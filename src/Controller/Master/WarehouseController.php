<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\Warehouse;
use App\Form\Master\WarehouseType;
use App\Grid\Master\WarehouseGridType;
use App\Repository\Master\WarehouseRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/warehouse')]
class WarehouseController extends AbstractController
{
    #[Route('/_list', name: 'app_master_warehouse__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WarehouseRepository $warehouseRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WarehouseGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $warehouses) = $warehouseRepository->fetchData($criteria);

        return $this->renderForm("master/warehouse/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'warehouses' => $warehouses,
        ]);
    }

    #[Route('/', name: 'app_master_warehouse_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/warehouse/index.html.twig");
    }

    #[Route('/new', name: 'app_master_warehouse_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, WarehouseRepository $warehouseRepository): Response
    {
        $warehouse = new Warehouse();
        $form = $this->createForm(WarehouseType::class, $warehouse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $warehouseRepository->add($warehouse, true);

            return $this->redirectToRoute('app_master_warehouse_show', ['id' => $warehouse->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/warehouse/new.html.twig', [
            'warehouse' => $warehouse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_warehouse_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Warehouse $warehouse): Response
    {
        return $this->render('master/warehouse/show.html.twig', [
            'warehouse' => $warehouse,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_warehouse_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Warehouse $warehouse, WarehouseRepository $warehouseRepository): Response
    {
        $form = $this->createForm(WarehouseType::class, $warehouse);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $warehouseRepository->add($warehouse, true);

            return $this->redirectToRoute('app_master_warehouse_show', ['id' => $warehouse->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/warehouse/edit.html.twig', [
            'warehouse' => $warehouse,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_warehouse_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Warehouse $warehouse, WarehouseRepository $warehouseRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $warehouse->getId(), $request->request->get('_token'))) {
            $warehouseRepository->remove($warehouse, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_warehouse_index', [], Response::HTTP_SEE_OTHER);
    }
}
