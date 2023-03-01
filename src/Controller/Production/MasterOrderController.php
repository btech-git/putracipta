<?php

namespace App\Controller\Production;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Entity\Production\MasterOrder;
use App\Form\Production\MasterOrderType;
use App\Grid\Production\MasterOrderGridType;
use App\Repository\Production\MasterOrderRepository;
use App\Service\Production\MasterOrderFormService;
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
        $masterOrderFormService->initialize($masterOrder, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(MasterOrderType::class, $masterOrder);
        $form->handleRequest($request);
//        $registerNewProductFormService->finalize($registerNewProduct);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
//            $masterOrderRepository->add($masterOrder, true);
            $masterOrderFormService->save($masterOrder);

            return $this->redirectToRoute('app_production_master_order_show', ['id' => $masterOrder->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/master_order/new.{$_format}.twig", [
            'masterOrder' => $masterOrder,
            'form' => $form,
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
        $masterOrderFormService->initialize($masterOrder, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(MasterOrderType::class, $masterOrder);
        $form->handleRequest($request);
//        $masterOrderFormService->finalize($masterOrder);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
//            $masterOrderRepository->add($masterOrder, true);
            $masterOrderFormService->save($masterOrder);

            return $this->redirectToRoute('app_production_master_order_show', ['id' => $masterOrder->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("production/master_order/edit.{$_format}.twig", [
            'masterOrder' => $masterOrder,
            'form' => $form,
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
