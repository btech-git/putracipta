<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\DeliveryHeader;
use App\Form\Transaction\DeliveryHeaderType;
use App\Grid\Transaction\DeliveryHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Transaction\DeliveryHeaderRepository;
use App\Service\Transaction\DeliveryHeaderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/delivery_header')]
class DeliveryHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_delivery_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DeliveryHeaderRepository $deliveryHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(DeliveryHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $deliveryHeaders) = $deliveryHeaderRepository->fetchData($criteria);

        return $this->renderForm("transaction/delivery_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'deliveryHeaders' => $deliveryHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_delivery_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/delivery_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_transaction_delivery_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, DeliveryHeaderFormService $deliveryHeaderFormService, $_format = 'html'): Response
    {
        $deliveryHeader = new DeliveryHeader();
        $deliveryHeaderFormService->initialize($deliveryHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(DeliveryHeaderType::class, $deliveryHeader);
        $form->handleRequest($request);
        $deliveryHeaderFormService->finalize($deliveryHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $deliveryHeaderFormService->save($deliveryHeader);

            return $this->redirectToRoute('app_transaction_delivery_header_show', ['id' => $deliveryHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/delivery_header/new.{$_format}.twig", [
            'deliveryHeader' => $deliveryHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_delivery_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(DeliveryHeader $deliveryHeader): Response
    {
        return $this->render('transaction/delivery_header/show.html.twig', [
            'deliveryHeader' => $deliveryHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_delivery_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, DeliveryHeader $deliveryHeader, DeliveryHeaderFormService $deliveryHeaderFormService, $_format = 'html'): Response
    {
        $deliveryHeaderFormService->initialize($deliveryHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(DeliveryHeaderType::class, $deliveryHeader);
        $form->handleRequest($request);
        $deliveryHeaderFormService->finalize($deliveryHeader);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $deliveryHeaderFormService->save($deliveryHeader);

            return $this->redirectToRoute('app_transaction_delivery_header_show', ['id' => $deliveryHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/delivery_header/edit.{$_format}.twig", [
            'deliveryHeader' => $deliveryHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_delivery_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, DeliveryHeader $deliveryHeader, DeliveryHeaderRepository $deliveryHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $deliveryHeader->getId(), $request->request->get('_token'))) {
            $deliveryHeaderRepository->remove($deliveryHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_delivery_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/memo', name: 'app_transaction_delivery_header_memo', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memo(DeliveryHeader $deliveryHeader, LiteralConfigRepository $literalConfigRepository): Response
    {
        $fileName = 'delivery.pdf';
        $htmlView = $this->renderView('transaction/delivery_header/memo.html.twig', [
            'deliveryHeader' => $deliveryHeader,
            'ifscCode' => $literalConfigRepository->findLiteralValue('ifscCode'),
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/assets/styles/', 'memo.css');
        $pdfGenerator->generate($htmlView, $fileName);
    }
}
