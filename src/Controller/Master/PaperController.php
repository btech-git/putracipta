<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\Paper;
use App\Form\Master\PaperType;
use App\Grid\Master\PaperGridType;
use App\Repository\Master\PaperRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/paper')]
class PaperController extends AbstractController
{
    #[Route('/_list', name: 'app_master_paper__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PaperRepository $paperRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PaperGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $papers) = $paperRepository->fetchData($criteria);

        return $this->renderForm("master/paper/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'papers' => $papers,
        ]);
    }

    #[Route('/', name: 'app_master_paper_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/paper/index.html.twig");
    }

    #[Route('/new', name: 'app_master_paper_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PaperRepository $paperRepository): Response
    {
        $paper = new Paper();
        $form = $this->createForm(PaperType::class, $paper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paperRepository->add($paper, true);

            return $this->redirectToRoute('app_master_paper_show', ['id' => $paper->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/paper/new.html.twig', [
            'paper' => $paper,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_paper_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Paper $paper): Response
    {
        return $this->render('master/paper/show.html.twig', [
            'paper' => $paper,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_paper_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, Paper $paper, PaperRepository $paperRepository): Response
    {
        $form = $this->createForm(PaperType::class, $paper);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paperRepository->add($paper, true);

            return $this->redirectToRoute('app_master_paper_show', ['id' => $paper->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/paper/edit.html.twig', [
            'paper' => $paper,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_paper_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, Paper $paper, PaperRepository $paperRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $paper->getId(), $request->request->get('_token'))) {
            $paperRepository->remove($paper, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_paper_index', [], Response::HTTP_SEE_OTHER);
    }
}
