<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\MachinePrinting;
use App\Form\Master\MachinePrintingType;
use App\Grid\Master\MachinePrintingGridType;
use App\Repository\Master\MachinePrintingRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/machine_printing')]
class MachinePrintingController extends AbstractController
{
    #[Route('/_list', name: 'app_master_machine_printing__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MachinePrintingRepository $machinePrintingRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(MachinePrintingGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $machinePrintings) = $machinePrintingRepository->fetchData($criteria);

        return $this->renderForm("master/machine_printing/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'machinePrintings' => $machinePrintings,
        ]);
    }

    #[Route('/', name: 'app_master_machine_printing_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/machine_printing/index.html.twig");
    }

    #[Route('/new', name: 'app_master_machine_printing_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, MachinePrintingRepository $machinePrintingRepository): Response
    {
        $machinePrinting = new MachinePrinting();
        $form = $this->createForm(MachinePrintingType::class, $machinePrinting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $machinePrintingRepository->add($machinePrinting, true);

            return $this->redirectToRoute('app_master_machine_printing_show', ['id' => $machinePrinting->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/machine_printing/new.html.twig', [
            'machinePrinting' => $machinePrinting,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_machine_printing_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(MachinePrinting $machinePrinting): Response
    {
        return $this->render('master/machine_printing/show.html.twig', [
            'machinePrinting' => $machinePrinting,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_machine_printing_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, MachinePrinting $machinePrinting, MachinePrintingRepository $machinePrintingRepository): Response
    {
        $form = $this->createForm(MachinePrintingType::class, $machinePrinting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $machinePrintingRepository->add($machinePrinting, true);

            return $this->redirectToRoute('app_master_machine_printing_show', ['id' => $machinePrinting->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/machine_printing/edit.html.twig', [
            'machinePrinting' => $machinePrinting,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_machine_printing_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, MachinePrinting $machinePrinting, MachinePrintingRepository $machinePrintingRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $machinePrinting->getId(), $request->request->get('_token'))) {
            $machinePrintingRepository->remove($machinePrinting, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_machine_printing_index', [], Response::HTTP_SEE_OTHER);
    }
}