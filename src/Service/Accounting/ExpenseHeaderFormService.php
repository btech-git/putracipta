<?php

namespace App\Service\Accounting;

use App\Entity\Accounting\ExpenseDetail;
use App\Entity\Accounting\ExpenseHeader;
use App\Repository\Accounting\ExpenseDetailRepository;
use App\Repository\Accounting\ExpenseHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class ExpenseHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private ExpenseHeaderRepository $expenseHeaderRepository;
    private ExpenseDetailRepository $expenseDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->expenseHeaderRepository = $entityManager->getRepository(ExpenseHeader::class);
        $this->expenseDetailRepository = $entityManager->getRepository(ExpenseDetail::class);
    }

    public function initialize(ExpenseHeader $expenseHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($expenseHeader->getId())) {
            $lastExpenseHeader = $this->expenseHeaderRepository->findRecentBy($year, $month);
            $currentExpenseHeader = ($lastExpenseHeader === null) ? $expenseHeader : $lastExpenseHeader;
            $expenseHeader->setCodeNumberToNext($currentExpenseHeader->getCodeNumber(), $year, $month);

            $expenseHeader->setCreatedTransactionDateTime($datetime);
            $expenseHeader->setCreatedTransactionUser($user);
        } else {
            $expenseHeader->setModifiedTransactionDateTime($datetime);
            $expenseHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(ExpenseHeader $expenseHeader, array $options = []): void
    {
        $expenseHeader->setTotalAmount($expenseHeader->getSyncTotalAmount());        
    }

    public function save(ExpenseHeader $expenseHeader, array $options = []): void
    {
        $this->expenseHeaderRepository->add($expenseHeader);
        foreach ($expenseHeader->getExpenseDetails() as $expenseDetail) {
            $this->expenseDetailRepository->add($expenseDetail);
        }
        $this->entityManager->flush();
    }
}
