<?php

namespace App\Controller\Transaction;

use App\Common\Data\Criteria\DataCriteria;
use App\Entity\Transaction\SaleOrderHeader;
use App\Form\Transaction\SaleOrderHeaderType;
use App\Grid\Transaction\SaleOrderHeaderGridType;
use App\Repository\Admin\LiteralConfigRepository;
use App\Repository\Transaction\SaleOrderHeaderRepository;
use App\Service\Transaction\SaleOrderHeaderFormService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/transaction/sale_order_header')]
class SaleOrderHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_transaction_sale_order_header__list', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function _list(Request $request, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $form = $this->createForm(SaleOrderHeaderGridType::class, $criteria, ['method' => 'GET']);
        $form->handleRequest($request);

        list($count, $saleOrderHeaders) = $saleOrderHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->addOrderBy("{$alias}.transactionDate", 'DESC');
            $qb->addOrderBy("{$alias}.id", 'DESC');
            if (isset($request->query->get('sale_order_header_grid')['filter']['customer:company']) && isset($request->query->get('sale_order_header_grid')['sort']['customer:company'])) {
                $qb->innerJoin("{$alias}.customer", 's');
                $add['filter']($qb, 's', 'company', $request->query->get('sale_order_header_grid')['filter']['customer:company']);
                $add['sort']($qb, 's', 'company', $request->query->get('sale_order_header_grid')['sort']['customer:company']);
            }
        });

        return $this->renderForm("transaction/sale_order_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'saleOrderHeaders' => $saleOrderHeaders,
        ]);
    }

    #[Route('/', name: 'app_transaction_sale_order_header_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(): Response
    {
        return $this->render("transaction/sale_order_header/index.html.twig");
    }

    #[Route('/new.{_format}', name: 'app_transaction_sale_order_header_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, SaleOrderHeaderFormService $saleOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $saleOrderHeader = new SaleOrderHeader();
        $saleOrderHeaderFormService->initialize($saleOrderHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SaleOrderHeaderType::class, $saleOrderHeader);
        $form->handleRequest($request);
        $saleOrderHeaderFormService->finalize($saleOrderHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
//            $transactionFile = $form->get('transactionFile')->getData();
//            if ($transactionFile) {
//                $fileExtension = $transactionFile->guessExtension();
//                $saleOrderHeader->setTransactionFileExtension($fileExtension);
                $saleOrderHeaderFormService->save($saleOrderHeader);
//                try {
//                    $dir = $this->getParameter('kernel.project_dir') . '/public/uploads/sale-order';
//                    $filename = $saleOrderHeader->getId() . '.' . $fileExtension;
//                    $transactionFile->move($dir, $filename);
//                } catch (FileException $e) {
//                    // ... handle exception if something happens during file upload
//                }
//            }

            return $this->redirectToRoute('app_transaction_sale_order_header_show', ['id' => $saleOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/sale_order_header/new.{$_format}.twig", [
            'saleOrderHeader' => $saleOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_transaction_sale_order_header_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(SaleOrderHeader $saleOrderHeader): Response
    {
        return $this->render('transaction/sale_order_header/show.html.twig', [
            'saleOrderHeader' => $saleOrderHeader,
        ]);
    }

    #[Route('/{id}/edit.{_format}', name: 'app_transaction_sale_order_header_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, SaleOrderHeader $saleOrderHeader, SaleOrderHeaderFormService $saleOrderHeaderFormService, LiteralConfigRepository $literalConfigRepository, $_format = 'html'): Response
    {
        $saleOrderHeaderFormService->initialize($saleOrderHeader, ['year' => date('y'), 'month' => date('m'), 'datetime' => new \DateTime(), 'user' => $this->getUser()]);
        $form = $this->createForm(SaleOrderHeaderType::class, $saleOrderHeader);
        $form->handleRequest($request);
        $saleOrderHeaderFormService->finalize($saleOrderHeader, ['vatPercentage' => $literalConfigRepository->findLiteralValue('vatPercentage')]);

        if ($_format === 'html' && $form->isSubmitted() && $form->isValid()) {
            $saleOrderHeaderFormService->save($saleOrderHeader);

            return $this->redirectToRoute('app_transaction_sale_order_header_show', ['id' => $saleOrderHeader->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm("transaction/sale_order_header/edit.{$_format}.twig", [
            'saleOrderHeader' => $saleOrderHeader,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_transaction_sale_order_header_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Request $request, SaleOrderHeader $saleOrderHeader, SaleOrderHeaderRepository $saleOrderHeaderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $saleOrderHeader->getId(), $request->request->get('_token'))) {
            $saleOrderHeaderRepository->remove($saleOrderHeader, true);

            $this->addFlash('success', array('title' => 'Success!', 'message' => 'The record was deleted successfully.'));
        } else {
            $this->addFlash('danger', array('title' => 'Error!', 'message' => 'Failed to delete the record.'));
        }

        return $this->redirectToRoute('app_transaction_sale_order_header_index', [], Response::HTTP_SEE_OTHER);
    }
}
