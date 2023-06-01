<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\WorkOrderDistribution;
use App\Form\Master\WorkOrderDistributionType;
use App\Grid\Master\WorkOrderDistributionGridType;
use App\Repository\Master\WorkOrderDistributionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/work_order_distribution')]
class WorkOrderDistributionController extends AbstractController
{
    #[Route('/_list', name: 'app_master_work_order_distribution__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, WorkOrderDistributionRepository $workOrderDistributionRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(WorkOrderDistributionGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $workOrderDistributions) = $workOrderDistributionRepository->fetchData($criteria);

        return $this->renderForm("master/work_order_distribution/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'workOrderDistributions' => $workOrderDistributions,
        ]);
    }

    #[Route('/', name: 'app_master_work_order_distribution_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/work_order_distribution/index.html.twig");
    }

    #[Route('/new', name: 'app_master_work_order_distribution_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, WorkOrderDistributionRepository $workOrderDistributionRepository): Response
    {
        $workOrderDistribution = new WorkOrderDistribution();
        $form = $this->createForm(WorkOrderDistributionType::class, $workOrderDistribution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workOrderDistributionRepository->add($workOrderDistribution, true);

            return $this->redirectToRoute('app_master_work_order_distribution_show', ['id' => $workOrderDistribution->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/work_order_distribution/new.html.twig', [
            'workOrderDistribution' => $workOrderDistribution,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_work_order_distribution_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(WorkOrderDistribution $workOrderDistribution): Response
    {
        return $this->render('master/work_order_distribution/show.html.twig', [
            'workOrderDistribution' => $workOrderDistribution,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_work_order_distribution_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, WorkOrderDistribution $workOrderDistribution, WorkOrderDistributionRepository $workOrderDistributionRepository): Response
    {
        $form = $this->createForm(WorkOrderDistributionType::class, $workOrderDistribution);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $workOrderDistributionRepository->add($workOrderDistribution, true);

            return $this->redirectToRoute('app_master_work_order_distribution_show', ['id' => $workOrderDistribution->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/work_order_distribution/edit.html.twig', [
            'workOrderDistribution' => $workOrderDistribution,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_work_order_distribution_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, WorkOrderDistribution $workOrderDistribution, WorkOrderDistributionRepository $workOrderDistributionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $workOrderDistribution->getId(), $request->request->get('_token'))) {
            $workOrderDistributionRepository->remove($workOrderDistribution, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_work_order_distribution_index', [], Response::HTTP_SEE_OTHER);
    }
}