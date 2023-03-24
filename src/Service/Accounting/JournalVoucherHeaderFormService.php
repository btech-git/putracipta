<?php

namespace App\Service\Accounting;

use App\Entity\Accounting\JournalVoucherDetail;
use App\Entity\Accounting\JournalVoucherHeader;
use App\Repository\Accounting\JournalVoucherDetailRepository;
use App\Repository\Accounting\JournalVoucherHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class JournalVoucherHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private JournalVoucherHeaderRepository $journalVoucherHeaderRepository;
    private JournalVoucherDetailRepository $journalVoucherDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->journalVoucherHeaderRepository = $entityManager->getRepository(JournalVoucherHeader::class);
        $this->journalVoucherDetailRepository = $entityManager->getRepository(JournalVoucherDetail::class);
    }

    public function initialize(JournalVoucherHeader $journalVoucherHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($journalVoucherHeader->getId())) {
            $journalVoucherHeader->setCreatedAccountingDateTime($datetime);
            $journalVoucherHeader->setCreatedAccountingUser($user);
        } else {
            $journalVoucherHeader->setModifiedAccountingDateTime($datetime);
            $journalVoucherHeader->setModifiedAccountingUser($user);
        }
    }

    public function finalize(JournalVoucherHeader $journalVoucherHeader, array $options = []): void
    {
        if ($journalVoucherHeader->getTransactionDate() !== null) {
            $year = $journalVoucherHeader->getTransactionDate()->format('y');
            $month = $journalVoucherHeader->getTransactionDate()->format('m');
            $lastJournalVoucherHeader = $this->journalVoucherHeaderRepository->findRecentBy($year, $month);
            $currentJournalVoucherHeader = ($lastJournalVoucherHeader === null) ? $journalVoucherHeader : $lastJournalVoucherHeader;
            $journalVoucherHeader->setCodeNumberToNext($currentJournalVoucherHeader->getCodeNumber(), $year, $month);

        }
        
    }

    public function save(JournalVoucherHeader $journalVoucherHeader, array $options = []): void
    {
        $this->journalVoucherHeaderRepository->add($journalVoucherHeader);
        foreach ($journalVoucherHeader->getJournalVoucherDetails() as $journalVoucherDetail) {
            $this->journalVoucherDetailRepository->add($journalVoucherDetail);
        }
        $this->entityManager->flush();
    }
}
