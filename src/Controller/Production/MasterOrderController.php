<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Production\MasterOrder;
use App\Form\Production\MasterOrderType;
use App\Grid\Production\MasterOrderGridType;
use App\Repository\Production\MasterOrderRepository;
use App\Service\Production\MasterOrderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/production/master_order')]
class MasterOrderController extends AbstractController
{
    #[Route('/_list', name: 'app_production_master_order__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, MasterOrderRepository $masterOrderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'productionDate' => SortDescending::class,
            'id' => SortDescending::class,
        ]);
        $form = $this->createForm(MasterOrderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $masterOrders) = $masterOrderRepository->fetchData($criteria);

        return $this->renderForm("production/master_order/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'masterOrders' => $masterOrders,
        ]);
    }

    #[Route('/', name: 'app_production_master_order_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("production/master_order/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_production_master_order_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, MasterOrderFormService $masterOrderFormService, $_format = 'html'): Response
    {
        $masterOrder = new MasterOrder();
        $masterOrderFormService->initialize($masterOrder, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(MasterOrderType::class, $masterOrder);
        $form->handleRequest($request);
        $masterOrderFormService->finalize($masterOrder, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $masterOrderFormService->save($masterOrder);
            $masterOrderFormService->uploadFile($masterOrder, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/master-order');

            return $this->redirectToRoute('app_production_master_order_show', ['id' => $masterOrder->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/master_order/new.{$_format}.twig", [
            'masterOrder' => $masterOrder,
            'form' => $form,
            'transactionFileExists' => false,
        ]);
    }

    #[Route('/{id}', name: 'app_production_master_order_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(MasterOrder $masterOrder): Response
    {
        return $this->render('production/master_order/show.html.twig', [
            'masterOrder' => $masterOrder,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_production_master_order_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, MasterOrder $masterOrder, MasterOrderFormService $masterOrderFormService, $_format = 'html'): Response
    {
        $masterOrderFormService->initialize($masterOrder, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(MasterOrderType::class, $masterOrder);
        $form->handleRequest($request);
        $masterOrderFormService->finalize($masterOrder, ['transactionFile' => $form->get('transactionFile')->getData()]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $masterOrderFormService->save($masterOrder);
            $masterOrderFormService->uploadFile($masterOrder, $form->get('transactionFile')->getData(), $this->getParameter('kernel.project_dir') . '/public/uploads/master-order');

            return $this->redirectToRoute('app_production_master_order_show', ['id' => $masterOrder->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/master_order/edit.{$_format}.twig", [
            'masterOrder' => $masterOrder,
            'form' => $form,
            'transactionFileExists' => file_exists($this->getParameter('kernel.project_dir') . '/public/uploads/master-order/' . $masterOrder->getId() . '.' . $masterOrder->getLayoutModelFileExtension()),
        ]);
    }

    #[Route('/{id}/memo_master_order', name: 'app_production_master_order_memo_master_order', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memoMasterOrder(MasterOrder $masterOrder): Response
    {
        $fileName = 'master_order.pdf';
        $htmlView = $this->renderView('production/master_order/memo_master_order.html.twig', [
            'masterOrder' => $masterOrder,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="logo"(.+)src=".+">/', '<img id="logo"\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="upload"(.+)src=".+">/', '<img id="upload"\1src="' . $chrootDir . 'uploads/master-order/' . $masterOrder->getId() . '.' . $masterOrder->getLayoutModelFileExtension() . '">', $html),
        ]);
    }
    
    #[Route('/{id}/memo_work_order', name: 'app_production_master_order_memo_work_order', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memoWorkOrder(MasterOrder $masterOrder): Response
    {
        $fileName = 'work_order.pdf';
        $htmlView = $this->renderView('production/master_order/memo_work_order.html.twig', [
            'masterOrder' => $masterOrder,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="logo"(.+)src=".+">/', '<img id="logo"\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="upload"(.+)src=".+">/', '<img id="upload"\1src="' . $chrootDir . 'uploads/master-order/' . $masterOrder->getId() . '.' . $masterOrder->getLayoutModelFileExtension() . '">', $html),
        ]);
    }
    
    #[Route('/{id}/memo_work_order_color_mixing', name: 'app_production_master_order_memo_work_order_color_mixing', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memoWorkOrderColorMixing(MasterOrder $masterOrder): Response
    {
        $fileName = 'work_order_color_mixing.pdf';
        $htmlView = $this->renderView('production/master_order/memo_work_order_color_mixing.html.twig', [
            'masterOrder' => $masterOrder,
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img id="logo"(.+)src=".+">/', '<img id="logo"\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
        ]);
    }
    
    #[Route('/{id}/delete', name: 'app_production_master_order_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, MasterOrder $masterOrder, MasterOrderRepository $masterOrderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $masterOrder->getId(), $request->request->get('_token'))) {
            $masterOrderRepository->remove($masterOrder, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_production_master_order_index', [], Response::HTTP_SEE_OTHER);
    }
}
