<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Production\QualityControlSortingDetail;
use App\Grid\Report\QualityControlSortingGridType;
use App\Repository\Production\QualityControlSortingDetailRepository;
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

#[Route('/report/quality_control_sorting')]
class QualityControlSortingController extends AbstractController
{
    #[Route('/_list', name: 'app_report_quality_control_sorting__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function _list(Request $request, QualityControlSortingDetailRepository $qualityControlSortingDetailRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'qualityControlSortingHeader:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(QualityControlSortingGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $qualityControlSortings) = $qualityControlSortingDetailRepository->fetchData($criteria, function($qb, $alias, $add) use ($request, $criteria) {
            $qb->innerJoin("{$alias}.qualityControlSortingHeader", 'h');
            $qb->innerJoin("h.masterOrderHeader", 'm');
            $qb->addOrderBy("h.transactionDate", 'ASC');
            $qb->andWhere("{$alias}.isCanceled = false");
            $qb->andWhere("h.isCanceled = false");
            
            if (isset($request->request->get('quality_control_sorting_grid')['filter']['masterOrderHeader:codeNumberOrdinal']) && isset($request->request->get('quality_control_sorting_grid')['sort']['masterOrderHeader:codeNumberOrdinal'])) {
                $add['filter']($qb, 'm', 'codeNumberOrdinal', $request->request->get('quality_control_sorting_grid')['filter']['masterOrderHeader:codeNumberOrdinal']);
                $add['sort']($qb, 'm', 'codeNumberOrdinal', $request->request->get('quality_control_sorting_grid')['sort']['masterOrderHeader:codeNumberOrdinal']);
            }
            if (isset($request->request->get('quality_control_sorting_grid')['filter']['masterOrderHeader:codeNumberMonth']) && isset($request->request->get('quality_control_sorting_grid')['sort']['masterOrderHeader:codeNumberMonth'])) {
                $add['filter']($qb, 'm', 'codeNumberMonth', $request->request->get('quality_control_sorting_grid')['filter']['masterOrderHeader:codeNumberMonth']);
                $add['sort']($qb, 'm', 'codeNumberMonth', $request->request->get('quality_control_sorting_grid')['sort']['masterOrderHeader:codeNumberMonth']);
            }
            if (isset($request->request->get('quality_control_sorting_grid')['filter']['masterOrderHeader:codeNumberYear']) && isset($request->request->get('quality_control_sorting_grid')['sort']['masterOrderHeader:codeNumberYear'])) {
                $add['filter']($qb, 'm', 'codeNumberYear', $request->request->get('quality_control_sorting_grid')['filter']['masterOrderHeader:codeNumberYear']);
                $add['sort']($qb, 'm', 'codeNumberYear', $request->request->get('quality_control_sorting_grid')['sort']['masterOrderHeader:codeNumberYear']);
            }
            if (isset($request->request->get('quality_control_sorting_grid')['filter']['qualityControlSortingHeader:transactionDate']) && isset($request->request->get('quality_control_sorting_grid')['sort']['qualityControlSortingHeader:transactionDate'])) {
                $add['filter']($qb, 'h', 'transactionDate', $request->request->get('quality_control_sorting_grid')['filter']['qualityControlSortingHeader:transactionDate']);
                $add['sort']($qb, 'h', 'transactionDate', $request->request->get('quality_control_sorting_grid')['sort']['qualityControlSortingHeader:transactionDate']);
            }
            if (isset($request->request->get('quality_control_sorting_grid')['filter']['qualityControlSortingHeader:customer']) && isset($request->request->get('quality_control_sorting_grid')['sort']['qualityControlSortingHeader:customer'])) {
                $add['filter']($qb, 'h', 'customer', $request->request->get('quality_control_sorting_grid')['filter']['qualityControlSortingHeader:customer']);
                $add['sort']($qb, 'h', 'customer', $request->request->get('quality_control_sorting_grid')['sort']['qualityControlSortingHeader:customer']);
            }
            if (isset($request->request->get('quality_control_sorting_grid')['filter']['product:name']) && isset($request->request->get('quality_control_sorting_grid')['sort']['product:name'])) {
                $qb->innerJoin("{$alias}.product", 'p');
                $add['filter']($qb, 'p', 'name', $request->request->get('quality_control_sorting_grid')['filter']['product:name']);
                $add['sort']($qb, 'p', 'name', $request->request->get('quality_control_sorting_grid')['sort']['product:name']);
            }
        });

        if ($request->request->has('export')) {
            return $this->export($form, $qualityControlSortings);
        } else {
            return $this->renderForm("report/quality_control_sorting/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'qualityControlSortings' => $qualityControlSortings,
            ]);
        }
    }

    #[Route('/', name: 'app_report_quality_control_sorting_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_PRODUCTION_REPORT')]
    public function index(): Response
    {
        return $this->render("report/quality_control_sorting/index.html.twig");
    }

    public function export(FormInterface $form, array $qualityControlSortings): Response
    {
        $htmlString = $this->renderView("report/quality_control_sorting/_list_export.html.twig", [
            'form' => $form->createView(),
            'qualityControlSortings' => $qualityControlSortings,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'qc sortir.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
