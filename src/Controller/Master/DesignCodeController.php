<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DesignCode;
use App\Form\Master\DesignCodeType;
use App\Grid\Master\DesignCodeGridType;
use App\Service\Master\DesignCodeFormService;
use App\Repository\Master\DesignCodeRepository;
use App\Repository\Master\WorkOrderProcessRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/design_code')]
class DesignCodeController extends AbstractController
{
    #[Route('/_design_code_list', name: 'app_master_design_code__design_code_list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _designCodeList(Request $request, DesignCodeRepository $designCodeRepository): Response
    {
        $lastDesignCodes = $designCodeRepository->findBy(['customer' => $request->query->get('design_code')['customer']], ['id' => 'DESC'], 5, 0);

        return $this->render("master/design_code/_design_code_list.html.twig", [
            'lastDesignCodes' => $lastDesignCodes,
        ]);
    }

    #[Route('/_list', name: 'app_master_design_code__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DesignCodeRepository $designCodeRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(DesignCodeGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $designCodes) = $designCodeRepository->fetchData($criteria);

        return $this->renderForm("master/design_code/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'designCodes' => $designCodes,
        ]);
    }

    #[Route('/', name: 'app_master_design_code_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/design_code/index.html.twig");
    }

    #[Route('/new', name: 'app_master_design_code_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, DesignCodeFormService $designCodeFormService, WorkOrderProcessRepository $workOrderProcessRepository): Response
    {
        $designCode = new DesignCode();
        $form = $this->createForm(DesignCodeType::class, $designCode);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $designCodeFormService->save($designCode);

            return $this->redirectToRoute('app_master_design_code_show', ['id' => $designCode->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("master/design_code/new.html.twig", [
            'designCode' => $designCode,
            'form' => $form,
            'workOrderProcesses' => $workOrderProcessRepository->findAll(),
            'lastDesignCodes' => [],
        ]);
    }

    #[Route('/{id}', name: 'app_master_design_code_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(DesignCode $designCode): Response
    {
        return $this->render('master/design_code/show.html.twig', [
            'designCode' => $designCode,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_design_code_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, DesignCode $designCode, DesignCodeRepository $designCodeRepository, DesignCodeFormService $designCodeFormService, WorkOrderProcessRepository $workOrderProcessRepository): Response
    {
        $form = $this->createForm(DesignCodeType::class, $designCode);
        $form->handleRequest($request);

        if (IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $designCodeFormService->save($designCode);

            return $this->redirectToRoute('app_master_design_code_show', ['id' => $designCode->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/design_code/edit.html.twig', [
            'designCode' => $designCode,
            'form' => $form,
            'workOrderProcesses' => $workOrderProcessRepository->findAll(),
            'lastDesignCodes' => $designCodeRepository->findBy(['customer' => $designCode->getCustomer()], ['id' => 'DESC'], 5, 0),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_design_code_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, DesignCode $designCode, DesignCodeRepository $designCodeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $designCode->getId(), $request->request->get('_token'))) {
            $designCodeRepository->remove($designCode, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_design_code_index', [], Response::HTTP_SEE_OTHER);
    }
}
