<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\DiecutKnife;
use App\Form\Master\DiecutKnifeType;
use App\Grid\Master\DiecutKnifeGridType;
use App\Repository\Master\DiecutKnifeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/diecut_knife')]
class DiecutKnifeController extends AbstractController
{
    #[Route('/_list', name: 'app_master_diecut_knife__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DiecutKnifeRepository $diecutKnifeRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(DiecutKnifeGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $diecutKnives) = $diecutKnifeRepository->fetchData($criteria);

        return $this->renderForm("master/diecut_knife/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'diecutKnives' => $diecutKnives,
        ]);
    }

    #[Route('/', name: 'app_master_diecut_knife_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/diecut_knife/index.html.twig");
    }

    #[Route('/new', name: 'app_master_diecut_knife_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, DiecutKnifeRepository $diecutKnifeRepository): Response
    {
        $diecutKnife = new DiecutKnife();
        $form = $this->createForm(DiecutKnifeType::class, $diecutKnife);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $diecutKnifeRepository->add($diecutKnife, true);

            return $this->redirectToRoute('app_master_diecut_knife_show', ['id' => $diecutKnife->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/diecut_knife/new.html.twig', [
            'diecutKnife' => $diecutKnife,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_diecut_knife_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(DiecutKnife $diecutKnife): Response
    {
        return $this->render('master/diecut_knife/show.html.twig', [
            'diecutKnife' => $diecutKnife,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_diecut_knife_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, DiecutKnife $diecutKnife, DiecutKnifeRepository $diecutKnifeRepository): Response
    {
        $form = $this->createForm(DiecutKnifeType::class, $diecutKnife);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $diecutKnifeRepository->add($diecutKnife, true);

            return $this->redirectToRoute('app_master_diecut_knife_show', ['id' => $diecutKnife->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/diecut_knife/edit.html.twig', [
            'diecutKnife' => $diecutKnife,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_diecut_knife_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, DiecutKnife $diecutKnife, DiecutKnifeRepository $diecutKnifeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $diecutKnife->getId(), $request->request->get('_token'))) {
            $diecutKnifeRepository->remove($diecutKnife, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_diecut_knife_index', [], Response::HTTP_SEE_OTHER);
    }
}