<?php

namespace App\Service\Stock;

use App\Entity\Stock\AdjustmentStockDetail;
use App\Entity\Stock\AdjustmentStockHeader;
use App\Repository\Stock\AdjustmentStockDetailRepository;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class AdjustmentStockHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository;
    private AdjustmentStockDetailRepository $adjustmentStockDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->adjustmentStockHeaderRepository = $entityManager->getRepository(AdjustmentStockHeader::class);
        $this->adjustmentStockDetailRepository = $entityManager->getRepository(AdjustmentStockDetail::class);
    }

    public function initialize(AdjustmentStockHeader $adjustmentStockHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($adjustmentStockHeader->getId())) {
            $adjustmentStockHeader->setCreatedTransactionDateTime($datetime);
            $adjustmentStockHeader->setCreatedTransactionUser($user);
        } else {
            $adjustmentStockHeader->setModifiedTransactionDateTime($datetime);
            $adjustmentStockHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(AdjustmentStockHeader $adjustmentStockHeader, array $options = []): void
    {
        if ($adjustmentStockHeader->getTransactionDate() !== null) {
            $year = $adjustmentStockHeader->getTransactionDate()->format('y');
            $month = $adjustmentStockHeader->getTransactionDate()->format('m');
            $lastAdjustmentStockHeader = $this->adjustmentStockHeaderRepository->findRecentBy($year, $month);
            $currentAdjustmentStockHeader = ($lastAdjustmentStockHeader === null) ? $adjustmentStockHeader : $lastAdjustmentStockHeader;
            $adjustmentStockHeader->setCodeNumberToNext($currentAdjustmentStockHeader->getCodeNumber(), $year, $month);

        }
        
    }

    public function save(AdjustmentStockHeader $adjustmentStockHeader, array $options = []): void
    {
        $this->adjustmentStockHeaderRepository->add($adjustmentStockHeader);
        foreach ($adjustmentStockHeader->getAdjustmentStockDetails() as $adjustmentStockDetail) {
            $this->adjustmentStockDetailRepository->add($adjustmentStockDetail);
        }
        $this->entityManager->flush();
    }
}
