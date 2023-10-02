<?php

namespace App\Service\Accounting;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Accounting\ExpenseDetail;
use App\Entity\Accounting\ExpenseHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Accounting\ExpenseDetailRepository;
use App\Repository\Accounting\ExpenseHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ExpenseHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private ExpenseHeaderRepository $expenseHeaderRepository;
    private ExpenseDetailRepository $expenseDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->expenseHeaderRepository = $entityManager->getRepository(ExpenseHeader::class);
        $this->expenseDetailRepository = $entityManager->getRepository(ExpenseDetail::class);
    }

    public function initialize(ExpenseHeader $expenseHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($expenseHeader->getId())) {
            $expenseHeader->setCreatedTransactionDateTime($datetime);
            $expenseHeader->setCreatedTransactionUser($user);
        } else {
            $expenseHeader->setModifiedTransactionDateTime($datetime);
            $expenseHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(ExpenseHeader $expenseHeader, array $options = []): void
    {
        if ($expenseHeader->getTransactionDate() !== null) {
            $year = $expenseHeader->getTransactionDate()->format('y');
            $month = $expenseHeader->getTransactionDate()->format('m');
            $lastExpenseHeader = $this->expenseHeaderRepository->findRecentBy($year, $month);
            $currentExpenseHeader = ($lastExpenseHeader === null) ? $expenseHeader : $lastExpenseHeader;
            $expenseHeader->setCodeNumberToNext($currentExpenseHeader->getCodeNumber(), $year, $month);

        }
        $expenseHeader->setTotalAmount($expenseHeader->getSyncTotalAmount());        
    }

    public function save(ExpenseHeader $expenseHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->expenseHeaderRepository->add($expenseHeader);
        foreach ($expenseHeader->getExpenseDetails() as $expenseDetail) {
            $this->expenseDetailRepository->add($expenseDetail);
        }
        $this->entityManager->flush();
    }
}
