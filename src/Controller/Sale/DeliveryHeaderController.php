<?php

namespace App\Controller\Sale;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\SortDescending;
use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Sale\DeliveryHeader;
use App\Form\Sale\DeliveryHeaderType;
use App\Grid\Sale\DeliveryHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Sale\DeliveryHeaderRepository;
use App\Service\Sale\DeliveryHeaderFormService;
use App\Util\PdfGenerator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sale/delivery_header')]
class DeliveryHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_sale_delivery_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, DeliveryHeaderRepository $deliveryHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $criteria->setSort([
            'transactionDate' => SortDescending::class,
        ]);
        $form = $this->createForm(DeliveryHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $deliveryHeaders) = $deliveryHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            if (isset($request->request->get('delivery_header_grid')['filter']['customer:company']) && isset($request->request->get('delivery_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->request->get('delivery_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->request->get('delivery_header_grid')['sort']['customer:company']);
            }
            if (isset($request->request->get('delivery_header_grid')['filter']['warehouse:name']) && isset($request->request->get('delivery_header_grid')['sort']['warehouse:name'])) {
                $qb->innerJoin("{$alias}.warehouse", 'w');
                $add['filter']($qb, 'w', 'name', $request->request->get('delivery_header_grid')['filter']['warehouse:name']);
                $add['sort']($qb, 'w', 'name', $request->request->get('delivery_header_grid')['sort']['warehouse:name']);
            }
        });

        return $this->renderForm("sale/delivery_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'deliveryHeaders' => $deliveryHeaders,
        ]);
    }

    #[Route('/', name: 'app_sale_delivery_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("sale/delivery_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_sale_delivery_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, DeliveryHeaderFormService $deliveryHeaderFormService, $_format = 'html'): Response
    {
        $deliveryHeader = new DeliveryHeader();
        $deliveryHeaderFormService->initialize($deliveryHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(DeliveryHeaderType::class, $deliveryHeader);
        $form->handleRequest($request);
        $deliveryHeaderFormService->finalize($deliveryHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $deliveryHeaderFormService->save($deliveryHeader);

            return $this->redirectToRoute('app_sale_delivery_header_show', ['id' => $deliveryHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("sale/delivery_header/new.{$_format}.twig", [
            'deliveryHeader' => $deliveryHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sale_delivery_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(DeliveryHeader $deliveryHeader): Response
    {
        return $this->render('sale/delivery_header/show.html.twig', [
            'deliveryHeader' => $deliveryHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_sale_delivery_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, DeliveryHeader $deliveryHeader, DeliveryHeaderFormService $deliveryHeaderFormService, $_format = 'html'): Response
    {
        $deliveryHeaderFormService->initialize($deliveryHeader, ['datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(DeliveryHeaderType::class, $deliveryHeader);
        $form->handleRequest($request);
        $deliveryHeaderFormService->finalize($deliveryHeader);

        if ($_format === 'html' && IdempotentUtility::check($request) && $form->isSubmitted() && $form->isValid()) {
            $deliveryHeaderFormService->save($deliveryHeader);

            return $this->redirectToRoute('app_sale_delivery_header_show', ['id' => $deliveryHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("sale/delivery_header/edit.{$_format}.twig", [
            'deliveryHeader' => $deliveryHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_sale_delivery_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, DeliveryHeader $deliveryHeader, DeliveryHeaderRepository $deliveryHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $deliveryHeader->getId(), $request->request->get('_token'))) {
            $deliveryHeaderRepository->remove($deliveryHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_sale_delivery_header_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/memo', name: 'app_sale_delivery_header_memo', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function memo(DeliveryHeader $deliveryHeader, LiteralConfigRepository $literalConfigRepository): Response
    {
        $fileName = 'delivery.pdf';
        $htmlView = $this->renderView('sale/delivery_header/memo.html.twig', [
            'deliveryHeader' => $deliveryHeader,
            'ifscCode' => $literalConfigRepository->findLiteralValue('ifscCode'),
        ]);

        $pdfGenerator = new PdfGenerator($this->getParameter('kernel.project_dir') . '/public/');
        $pdfGenerator->generate($htmlView, $fileName, [
            fn($html, $chrootDir) => preg_replace('/<link rel="stylesheet"(.+)href=".+">/', '<link rel="stylesheet"\1href="' . $chrootDir . 'build/memo.css">', $html),
            fn($html, $chrootDir) => preg_replace('/<img(.+)src=".+">/', '<img\1src="' . $chrootDir . 'images/Logo.jpg">', $html),
        ]);
    }
}
