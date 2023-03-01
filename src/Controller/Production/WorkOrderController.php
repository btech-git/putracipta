<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Production\WorkOrder;
use App\Form\Production\WorkOrderType;
use App\Grid\Production\WorkOrderGridType;
use App\Repository\Production\WorkOrderRepository;
use App\Service\Production\WorkOrderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/work_order')]
class WorkOrderController extends AbstractController
{
    #[Route('/_list', name: 'app_production_work_order__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WorkOrderRepository $workOrderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $workOrders) = $workOrderRepository->fetchData($criteria);

        return $this->renderForm("production/work_order/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrders' => $workOrders,
        ]);
    }

    #[Route('/', name: 'app_production_work_order_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("production/work_order/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_work_order_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, WorkOrderFormService $workOrderFormService, $_format = 'html'): Response
    {
        $workOrder = new WorkOrder();
        $workOrderFormService->initialize($workOrder, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderType::class, $workOrder);
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
//            $workOrderRepository->add($workOrder, true);
            $workOrderFormService->save($workOrder);

            return $this->redirectToRoute('app_production_work_order_show', ['id' => $workOrder->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order/new.{$_format}.twig", [
            'workOrder' => $workOrder,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_production_work_order_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(WorkOrder $workOrder): Response
    {
        return $this->render('production/work_order/show.html.twig', [
            'workOrder' => $workOrder,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_work_order_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, WorkOrder $workOrder, WorkOrderFormService $workOrderFormService, $_format = 'html'): Response
    {
        $workOrderFormService->initialize($workOrder, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderType::class, $workOrder);
        $form->handleRequest($request);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
//            $workOrderRepository->add($workOrder, true);
            $workOrderFormService->save($workOrder);

            return $this->redirectToRoute('app_production_work_order_show', ['id' => $workOrder->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order/edit.{$_format}.twig", [
            'workOrder' => $workOrder,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_work_order_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, WorkOrder $workOrder, WorkOrderRepository $workOrderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrder->getId(), $request->request->get('_token'))) {
            $workOrderRepository->remove($workOrder, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_work_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
