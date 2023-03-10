<?php

namespace App\Service\Accounting;

use App\Entity\Accounting\DepositDetail;
use App\Entity\Accounting\DepositHeader;
use App\Repository\Accounting\DepositDetailRepository;
use App\Repository\Accounting\DepositHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class DepositHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private DepositHeaderRepository $depositHeaderRepository;
    private DepositDetailRepository $depositDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->depositHeaderRepository = $entityManager->getRepository(DepositHeader::class);
        $this->depositDetailRepository = $entityManager->getRepository(DepositDetail::class);
    }

    public function initialize(DepositHeader $depositHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($depositHeader->getId())) {
            $lastDepositHeader = $this->depositHeaderRepository->findRecentBy($year, $month);
            $currentDepositHeader = ($lastDepositHeader === null) ? $depositHeader : $lastDepositHeader;
            $depositHeader->setCodeNumberToNext($currentDepositHeader->getCodeNumber(), $year, $month);

            $depositHeader->setCreatedTransactionDateTime($datetime);
            $depositHeader->setCreatedTransactionUser($user);
        } else {
            $depositHeader->setModifiedTransactionDateTime($datetime);
            $depositHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(DepositHeader $depositHeader, array $options = []): void
    {
        $depositHeader->setTotalAmount($depositHeader->getSyncTotalAmount());        
    }

    public function save(DepositHeader $depositHeader, array $options = []): void
    {
        $this->depositHeaderRepository->add($depositHeader);
        foreach ($depositHeader->getDepositDetails() as $depositDetail) {
            $this->depositDetailRepository->add($depositDetail);
        }
        $this->entityManager->flush();
    }
}
