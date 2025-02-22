<?php

namespace App\Controller\Report;

use App\Common\Data\Criteria\DataCriteria;
use App\Common\Data\Operator\FilterBetween;
use App\Entity\Accounting\AccountingLedger;
use App\Grid\Report\AccountingLedgerGridType;
use App\Repository\Accounting\AccountingLedgerRepository;
use App\Repository\Master\AccountRepository;
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

#[Route('/report/accounting_ledger')]
class AccountingLedgerController extends AbstractController
{
    #[Route('/_list', name: 'app_report_accounting_ledger__list', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function _list(Request $request, AccountRepository $accountRepository, AccountingLedgerRepository $accountingLedgerRepository): Response
    {
        $criteria = new DataCriteria();
        $currentDate = date('Y-m-d');
        $criteria->setFilter([
            'accountingLedger:transactionDate' => [FilterBetween::class, $currentDate, $currentDate],
        ]);
        $form = $this->createForm(AccountingLedgerGridType::class, $criteria);
        $form->handleRequest($request);

        list($count, $accounts) = $accountRepository->fetchData($criteria, function($qb, $alias) use ($criteria) {
            $qb->andWhere("{$alias}.isInactive = false");
            $qb->andWhere("EXISTS (SELECT l.id FROM " . AccountingLedger::class . " l WHERE {$alias} = l.account AND l.isReversed = false AND l.transactionDate BETWEEN :startDate AND :endDate)");
            $qb->setParameter('startDate', $criteria->getFilter()['accountingLedger:transactionDate'][1]);
            $qb->setParameter('endDate', $criteria->getFilter()['accountingLedger:transactionDate'][2]);
            $qb->addOrderBy("{$alias}.name", 'ASC');
        });
        $beginningBalanceList = $this->getBeginningBalanceList($accountingLedgerRepository, $criteria, $accounts);
        $accountingLedgers = $this->getAccountingLedgers($accountingLedgerRepository, $criteria, $accounts);

        if ($request->request->has('export')) {
            return $this->export($form, $accounts, $beginningBalanceList, $accountingLedgers);
        } else {
            return $this->renderForm("report/accounting_ledger/_list.html.twig", [
                'form' => $form,
                'count' => $count,
                'accounts' => $accounts,
                'beginningBalanceList' => $beginningBalanceList,
                'accountingLedgers' => $accountingLedgers,
            ]);
        }
    }

    #[Route('/', name: 'app_report_accounting_ledger_index', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_FINANCE_REPORT')]
    public function index(): Response
    {
        return $this->render("report/accounting_ledger/index.html.twig");
    }

    private function getBeginningBalanceList(AccountingLedgerRepository $accountingLedgerRepository, DataCriteria $criteria, array $accounts): array
    {
        $startDate = $criteria->getFilter()['accountingLedger:transactionDate'][1];
        $accountBeginningBalanceList = $accountingLedgerRepository->getAccountBeginningBalanceList($accounts, $startDate);
        $beginningBalanceList = [];
        foreach ($accountBeginningBalanceList as $accountBeginningBalanceItem) {
            $beginningBalanceList[$accountBeginningBalanceItem['accountId']] = $accountBeginningBalanceItem['beginningBalance'];
        }

        return $beginningBalanceList;
    }

    private function getAccountingLedgers(AccountingLedgerRepository $accountingLedgerRepository, DataCriteria $criteria, array $accounts): array
    {
        $startDate = $criteria->getFilter()['accountingLedger:transactionDate'][1];
        $endDate = $criteria->getFilter()['accountingLedger:transactionDate'][2];
        $accountingLedgers = $accountingLedgerRepository->findAccountingLedgers($accounts, $startDate, $endDate);
        $journalLedgers = [];
        foreach ($accountingLedgers as $accountingLedger) {
            $journalLedgers[$accountingLedger->getAccount()->getId()][] = $accountingLedger;
        }

        return $journalLedgers;
    }

    public function export(FormInterface $form, array $accounts, array $beginningBalanceList, array $accountingLedgers): Response
    {
        $htmlString = $this->renderView("report/accounting_ledger/_list_export.html.twig", [
            'form' => $form->createView(),
            'accounts' => $accounts,
            'beginningBalanceList' => $beginningBalanceList,
            'accountingLedgers' => $accountingLedgers,
        ]);

        $reader = new Html();
        $spreadsheet = $reader->loadFromString($htmlString);

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $response =  new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $filename = 'accounting_ledger.xlsx';
        $dispositionHeader = $response->headers->makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);
        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}