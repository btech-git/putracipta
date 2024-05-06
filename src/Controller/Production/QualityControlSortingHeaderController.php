<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Production\QualityControlSortingHeader;
use App\Form\Production\QualityControlSortingHeaderType;
use App\Grid\Production\QualityControlSortingHeaderGridType;
use App\Repository\Production\QualityControlSortingHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/quality_control_sorting_header')]
class QualityControlSortingHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_production_quality_control_sorting_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, QualityControlSortingHeaderRepository $qualityControlSortingHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(QualityControlSortingHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $qualityControlSortingHeaders) = $qualityControlSortingHeaderRepository->fetchData($criteria);

        return $this->renderForm("production/quality_control_sorting_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'qualityControlSortingHeaders' => $qualityControlSortingHeaders,
        ]);
    }

    #[Route('/', name: 'app_production_quality_control_sorting_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("production/quality_control_sorting_header/index.html.twig");
    }

    #[Route('/new', name: 'app_production_quality_control_sorting_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, QualityControlSortingHeaderRepository $qualityControlSortingHeaderRepository): Response
    {
        $qualityControlSortingHeader = new QualityControlSortingHeader();
        $form = $this->createForm(QualityControlSortingHeaderType::class, $qualityControlSortingHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $qualityControlSortingHeaderRepository->add($qualityControlSortingHeader, true);

            return $this->redirectToRoute('app_production_quality_control_sorting_header_show', ['id' => $qualityControlSortingHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('production/quality_control_sorting_header/new.html.twig', [
            'qualityControlSortingHeader' => $qualityControlSortingHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_production_quality_control_sorting_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(QualityControlSortingHeader $qualityControlSortingHeader): Response
    {
        return $this->render('production/quality_control_sorting_header/show.html.twig', [
            'qualityControlSortingHeader' => $qualityControlSortingHeader,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_production_quality_control_sorting_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, QualityControlSortingHeader $qualityControlSortingHeader, QualityControlSortingHeaderRepository $qualityControlSortingHeaderRepository): Response
    {
        $form = $this->createForm(QualityControlSortingHeaderType::class, $qualityControlSortingHeader);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $qualityControlSortingHeaderRepository->add($qualityControlSortingHeader, true);

            return $this->redirectToRoute('app_production_quality_control_sorting_header_show', ['id' => $qualityControlSortingHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('production/quality_control_sorting_header/edit.html.twig', [
            'qualityControlSortingHeader' => $qualityControlSortingHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_production_quality_control_sorting_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, QualityControlSortingHeader $qualityControlSortingHeader, QualityControlSortingHeaderRepository $qualityControlSortingHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $qualityControlSortingHeader->getId(), $request->request->get('_token'))) {
            $qualityControlSortingHeaderRepository->remove($qualityControlSortingHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_quality_control_sorting_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
