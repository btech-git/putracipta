<?php

namespace App\Repository\Accounting;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Accounting\AccountingLedger;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class AccountingLedgerRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AccountingLedger::class);
    }
    
    public function getAccountBeginningBalanceList(array $accounts, $startDate): array
    {
        $dql = "SELECT IDENTITY(e.account) AS accountId, SUM(e.debitAmount - e.creditAmount) AS beginningBalance
                FROM " . AccountingLedger::class . " e
                WHERE e.account IN (:accounts) AND e.isReversed = false AND e.transactionDate < :startDate
                GROUP BY e.account";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('accounts', $accounts);
        $query->setParameter('startDate', $startDate);
        $beginningBalanceList = $query->getScalarResult();

        return $beginningBalanceList;
    }

    public function findAccountingLedgers(array $accounts, $startDate, $endDate): array
    {
        $dql = "SELECT e
                FROM " . AccountingLedger::class . " e
                WHERE e.account IN (:accounts) AND e.isReversed = false AND e.transactionDate BETWEEN :startDate AND :endDate
                ORDER BY e.account ASC, e.transactionDate ASC";

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('accounts', $accounts);
        $query->setParameter('startDate', $startDate);
        $query->setParameter('endDate', $endDate);
        $journalLedgers = $query->getResult();

        return $journalLedgers;
    }
}
