<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DielineMillar;
use App\Form\Master\DielineMillarType;
use App\Grid\Master\DielineMillarGridType;
use App\Repository\Master\DielineMillarRepository;
use App\Service\Master\DielineMillarFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/dieline_millar')]
class DielineMillarController extends AbstractController
{
    #[Route('/_dieline_list', name: 'app_master_dieline_millar__dieline_list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _dielineList(Request $request, DielineMillarRepository $dielineMillarRepository): Response
    {
        $lastDielineMillars = $dielineMillarRepository->findBy(['customer' => $request->query->get('dieline_millar')['customer']], ['id' => 'DESC'], 5, 0);

        return $this->render("master/dieline_millar/_dieline_list.html.twig", [
            'lastDielineMillars' => $lastDielineMillars,
        ]);
    }

    #[Route('/_list', name: 'app_master_dieline_millar__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DielineMillarRepository $dielineMillarRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(DielineMillarGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $dielineMillars) = $dielineMillarRepository->fetchData($criteria);

        return $this->renderForm("master/dieline_millar/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'dielineMillars' => $dielineMillars,
        ]);
    }

    #[Route('/', name: 'app_master_dieline_millar_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/dieline_millar/index.html.twig");
    }

    #[Route('/new', name: 'app_master_dieline_millar_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, DielineMillarFormService $dielineMillarFormService): Response
    {
        $dielineMillar = new DielineMillar();
        $form = $this->createForm(DielineMillarType::class, $dielineMillar);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $dielineMillarFormService->save($dielineMillar);

            return $this->redirectToRoute('app_master_dieline_millar_show', ['id' => $dielineMillar->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/dieline_millar/new.html.twig', [
            'dielineMillar' => $dielineMillar,
            'form' => $form,
            'lastDielineMillars' => [],
        ]);
    }

    #[Route('/{id}', name: 'app_master_dieline_millar_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(DielineMillar $dielineMillar): Response
    {
        return $this->render('master/dieline_millar/show.html.twig', [
            'dielineMillar' => $dielineMillar,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_dieline_millar_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, DielineMillar $dielineMillar, DielineMillarRepository $dielineMillarRepository, DielineMillarFormService $dielineMillarFormService): Response
    {
        $form = $this->createForm(DielineMillarType::class, $dielineMillar);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $dielineMillarFormService->save($dielineMillar);

            return $this->redirectToRoute('app_master_dieline_millar_show', ['id' => $dielineMillar->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/dieline_millar/edit.html.twig', [
            'dielineMillar' => $dielineMillar,
            'form' => $form,
            'lastDielineMillars' => $dielineMillarRepository->findBy(['customer' => $dielineMillar->getCustomer()], ['id' => 'DESC'], 5, 0),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_dieline_millar_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, DielineMillar $dielineMillar, DielineMillarRepository $dielineMillarRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $dielineMillar->getId(), $request->request->get('_token'))) {
            $dielineMillarRepository->remove($dielineMillar, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_dieline_millar_index', [], Response::HTTP_SEE_OTHER);
    }
}
