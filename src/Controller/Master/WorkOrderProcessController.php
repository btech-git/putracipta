<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\WorkOrderProcess;
use App\Form\Master\WorkOrderProcessType;
use App\Grid\Master\WorkOrderProcessGridType;
use App\Repository\Master\WorkOrderProcessRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/work_order_process')]
class WorkOrderProcessController extends AbstractController
{
    #[Route('/_list', name: 'app_master_work_order_process__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WorkOrderProcessRepository $workOrderProcessRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderProcessGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $workOrderProcesses) = $workOrderProcessRepository->fetchData($criteria);

        return $this->renderForm("master/work_order_process/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrderProcesses' => $workOrderProcesses,
        ]);
    }

    #[Route('/', name: 'app_master_work_order_process_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/work_order_process/index.html.twig");
    }

    #[Route('/new', name: 'app_master_work_order_process_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, WorkOrderProcessRepository $workOrderProcessRepository): Response
    {
        $workOrderProcess = new WorkOrderProcess();
        $form = $this->createForm(WorkOrderProcessType::class, $workOrderProcess);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workOrderProcessRepository->add($workOrderProcess, true);

            return $this->redirectToRoute('app_master_work_order_process_show', ['id' => $workOrderProcess->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/work_order_process/new.html.twig', [
            'workOrderProcess' => $workOrderProcess,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_work_order_process_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(WorkOrderProcess $workOrderProcess): Response
    {
        return $this->render('master/work_order_process/show.html.twig', [
            'workOrderProcess' => $workOrderProcess,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_work_order_process_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, WorkOrderProcess $workOrderProcess, WorkOrderProcessRepository $workOrderProcessRepository): Response
    {
        $form = $this->createForm(WorkOrderProcessType::class, $workOrderProcess);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workOrderProcessRepository->add($workOrderProcess, true);

            return $this->redirectToRoute('app_master_work_order_process_show', ['id' => $workOrderProcess->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/work_order_process/edit.html.twig', [
            'workOrderProcess' => $workOrderProcess,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_work_order_process_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, WorkOrderProcess $workOrderProcess, WorkOrderProcessRepository $workOrderProcessRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrderProcess->getId(), $request->request->get('_token'))) {
            $workOrderProcessRepository->remove($workOrderProcess, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_work_order_process_index', [], Response::HTTP_SEE_OTHER);
    }
}