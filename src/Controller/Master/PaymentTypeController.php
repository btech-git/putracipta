<?php

namespace App\Controller\Master;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Master\PaymentType;
use App\Form\Master\PaymentTypeType;
use App\Grid\Master\PaymentTypeGridType;
use App\Repository\Master\PaymentTypeRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/master/payment_type')]
class PaymentTypeController extends AbstractController
{
    #[Route('/_list', name: 'app_master_payment_type__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, PaymentTypeRepository $paymentTypeRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(PaymentTypeGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $paymentTypes) = $paymentTypeRepository->fetchData($criteria);

        return $this->renderForm("master/payment_type/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'paymentTypes' => $paymentTypes,
        ]);
    }

    #[Route('/', name: 'app_master_payment_type_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("master/payment_type/index.html.twig");
    }

    #[Route('/new', name: 'app_master_payment_type_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PaymentTypeRepository $paymentTypeRepository): Response
    {
        $paymentType = new PaymentType();
        $form = $this->createForm(PaymentTypeType::class, $paymentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paymentTypeRepository->add($paymentType, true);

            return $this->redirectToRoute('app_master_payment_type_show', ['id' => $paymentType->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/payment_type/new.html.twig', [
            'paymentType' => $paymentType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_master_payment_type_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PaymentType $paymentType): Response
    {
        return $this->render('master/payment_type/show.html.twig', [
            'paymentType' => $paymentType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_master_payment_type_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, PaymentType $paymentType, PaymentTypeRepository $paymentTypeRepository): Response
    {
        $form = $this->createForm(PaymentTypeType::class, $paymentType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $paymentTypeRepository->add($paymentType, true);

            return $this->redirectToRoute('app_master_payment_type_show', ['id' => $paymentType->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('master/payment_type/edit.html.twig', [
            'paymentType' => $paymentType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_master_payment_type_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, PaymentType $paymentType, PaymentTypeRepository $paymentTypeRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $paymentType->getId(), $request->request->get('_token'))) {
            $paymentTypeRepository->remove($paymentType, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_master_payment_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
