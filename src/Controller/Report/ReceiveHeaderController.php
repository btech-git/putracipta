<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\ReceiveHeaderGridType;
use App\Repository\Purchase\ReceiveHeaderRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Html;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/receive_header')]
class ReceiveHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_receive_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function _list(Request $request, ReceiveHeaderRepository $receiveHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(ReceiveHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $receiveHeaders) = $receiveHeaderRepository->fetchData($criteria, function($qb, $alias, $add) use ($request) {
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->leftJoin("{$alias}.purchaseOrderHeader", 'm');
            $qb->leftJoin("{$alias}.purchaseOrderPaperHeader", 'p');
            if (isset($request->request->get('receive_header_grid')['filter']['purchaseOrderHeader:codeNumberOrdinal']) && isset($request->request->get('receive_header_grid')['sort']['purchaseOrderHeader:codeNumberOrdinal'])) {
                $add['filter']($qb, 'm', 'codeNumberOrdinal', $request->request->get('receive_header_grid')['filter']['purchaseOrderHeader:codeNumberOrdinal']);
                $add['sort']($qb, 'm', 'codeNumberOrdinal', $request->request->get('receive_header_grid')['sort']['purchaseOrderHeader:codeNumberOrdinal']);
            }
            if (isset($request->request->get('receive_header_grid')['filter']['purchaseOrderHeader:codeNumberMonth']) && isset($request->request->get('receive_header_grid')['sort']['purchaseOrderHeader:codeNumberMonth'])) {
                $add['filter']($qb, 'm', 'codeNumberMonth', $request->request->get('receive_header_grid')['filter']['purchaseOrderHeader:codeNumberMonth']);
                $add['sort']($qb, 'm', 'codeNumberMonth', $request->request->get('receive_header_grid')['sort']['purchaseOrderHeader:codeNumberMonth']);
            }
            if (isset($request->request->get('receive_header_grid')['filter']['purchaseOrderHeader:codeNumberYear']) && isset($request->request->get('receive_header_grid')['sort']['purchaseOrderHeader:codeNumberYear'])) {
                $add['filter']($qb, 'm', 'codeNumberYear', $request->request->get('receive_header_grid')['filter']['purchaseOrderHeader:codeNumberYear']);
                $add['sort']($qb, 'm', 'codeNumberYear', $request->request->get('receive_header_grid')['sort']['purchaseOrderHeader:codeNumberYear']);
            }
            if (isset($request->request->get('receive_header_grid')['filter']['purchaseOrderPaperHeader:codeNumberOrdinal']) && isset($request->request->get('receive_header_grid')['sort']['purchaseOrderPaperHeader:codeNumberOrdinal'])) {
                $add['filter']($qb, 'p', 'codeNumberOrdinal', $request->request->get('receive_header_grid')['filter']['purchaseOrderPaperHeader:codeNumberOrdinal']);
                $add['sort']($qb, 'p', 'codeNumberOrdinal', $request->request->get('receive_header_grid')['sort']['purchaseOrderPaperHeader:codeNumberOrdinal']);
            }
            if (isset($request->request->get('receive_header_grid')['filter']['purchaseOrderPaperHeader:codeNumberMonth']) && isset($request->request->get('receive_header_grid')['sort']['purchaseOrderPaperHeader:codeNumberMonth'])) {
                $add['filter']($qb, 'p', 'codeNumberMonth', $request->request->get('receive_header_grid')['filter']['purchaseOrderPaperHeader:codeNumberMonth']);
                $add['sort']($qb, 'p', 'codeNumberMonth', $request->request->get('receive_header_grid')['sort']['purchaseOrderPaperHeader:codeNumberMonth']);
            }
            if (isset($request->request->get('receive_header_grid')['filter']['purchaseOrderPaperHeader:codeNumberYear']) && isset($request->request->get('receive_header_grid')['sort']['purchaseOrderPaperHeader:codeNumberYear'])) {
                $add['filter']($qb, 'p', 'codeNumberYear', $request->request->get('receive_header_grid')['filter']['purchaseOrderPaperHeader:codeNumberYear']);
                $add['sort']($qb, 'p', 'codeNumberYear', $request->request->get('receive_header_grid')['sort']['purchaseOrderPaperHeader:codeNumberYear']);
            }
        });

        if ($request->request->has('export')) {
            return $this->export($form, $receiveHeaders);
        } else {
            return $this->renderForm("report/receive_header/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'receiveHeaders' => $receiveHeaders,
            ]);
        }
    }

    #[Route('/', name: 'app_report_receive_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_INVENTORY_MATERIAL_REPORT')]
    public function index(): Response
    {
        return $this->render("report/receive_header/index.html.twig");
    }

    public function export(FormInterface $form, array $receiveHeaders): Response
    {
        $htmlString = $this->renderView("report/receive_header/_list_export.html.twig", [
            'form' => $form->createView(),
            'receiveHeaders' => $receiveHeaders,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'receive.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}