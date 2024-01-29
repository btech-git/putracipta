<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Grid\Report\ExpenseHeaderGridType;
use App\Repository\Accounting\ExpenseHeaderRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/report/expense_header')]
class ExpenseHeaderController extends AbstractController
{
    #[Route('/_list', name: 'app_report_expense_header__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function _list(Request $request, ExpenseHeaderRepository $expenseHeaderRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(ExpenseHeaderGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $expenseHeaders) = $expenseHeaderRepository->fetchData($criteria);

        return $this->renderForm("report/expense_header/_list.html.twig", [
            'form' => $form,
            'count' => $count,
            'expenseHeaders' => $expenseHeaders,
        ]);
    }

    #[Route('/', name: 'app_report_expense_header_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/expense_header/index.html.twig");
    }

//    #[Route('/export', name: 'app_report_expense_header_export', methods: ['GET'])]
//    #[IsGranted('ROLE_USER')]
//    public function exportAction(Request $request)
//    {
//        $em = $this->getDoctrine()->getManager();
//        $repository = $em->getRepository(ExpenseHeader::class);
//
//        $grid = $this->get('lib.grid.datagrid');
//        $grid->build(ExpenseHeaderGridType::class, $repository, $request);
//
//        $excel = $this->get('phpexcel');
//        $excelXmlReader = $this->get('lib.excel.xml_reader');
//        $xml = $this->renderView('report/expense_header/export.xml.twig', array(
//            'grid' => $grid->createView(),
//        ));
//        $excelObject = $excelXmlReader->load($xml);
//        $writer = $excel->createWriter($excelObject, 'Excel5');
//        $response = $excel->createStreamedResponse($writer);
//
//        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, 'report.xls');
//        $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
//        $response->headers->set('Pragma', 'public');
//        $response->headers->set('Cache-Control', 'maxage=1');
//        $response->headers->set('Content-Disposition', $dispositionHeader);
//
//        return $response;
//    }
}
