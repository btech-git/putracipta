<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Production\WorkOrderHeader;
use App\Form\Production\WorkOrderHeaderType;
use App\Grid\Production\WorkOrderHeaderGridType;
use App\Repository\Production\WorkOrderHeaderRepository;
use App\Service\Production\WorkOrderHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/work_order_header')]
class WorkOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_production_work_order_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WorkOrderHeaderRepository $workOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $workOrderHeaders) = $workOrderHeaderRepository->fetchData($criteria);

        return $this->renderForm("production/work_order_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrderHeaders' => $workOrderHeaders,
        ]);
    }

    #[Route('/', name: 'app_production_work_order_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("production/work_order_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_work_order_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, WorkOrderHeaderFormService $workOrderHeaderFormService, $_format = 'html'): Response
    {
        $workOrderHeader = new WorkOrderHeader();
        $workOrderHeaderFormService->initialize($workOrderHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderHeaderType::class, $workOrderHeader);
        $form->handleRequest($request);
        $workOrderHeaderFormService->finalize($workOrderHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $workOrderHeaderFormService->save($workOrderHeader);

            return $this->redirectToRoute('app_production_work_order_header_show', ['id' => $workOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_header/new.{$_format}.twig", [
            'workOrderHeader' => $workOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_production_work_order_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(WorkOrderHeader $workOrderHeader): Response
    {
        return $this->render('production/work_order_header/show.html.twig', [
            'workOrderHeader' => $workOrderHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_work_order_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, WorkOrderHeader $workOrderHeader, WorkOrderHeaderFormService $workOrderHeaderFormService, $_format = 'html'): Response
    {
        $workOrderHeaderFormService->initialize($workOrderHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(WorkOrderHeaderType::class, $workOrderHeader);
        $form->handleRequest($request);
        $workOrderHeaderFormService->finalize($workOrderHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $workOrderHeaderFormService->save($workOrderHeader);

            return $this->redirectToRoute('app_production_work_order_header_show', ['id' => $workOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/work_order_header/edit.{$_format}.twig", [
            'workOrderHeader' => $workOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_work_order_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, WorkOrderHeader $workOrderHeader, WorkOrderHeaderRepository $workOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrderHeader->getId(), $request->request->get('_token'))) {
            $workOrderHeaderRepository->remove($workOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_work_order_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
