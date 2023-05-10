<?php

namespace App\Service\Stock;

use App\Entity\Stock\StockTransferDetail;
use App\Entity\Stock\StockTransferHeader;
use App\Repository\Stock\StockTransferDetailRepository;
use App\Repository\Stock\StockTransferHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class StockTransferHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private StockTransferHeaderRepository $stockTransferHeaderRepository;
    private StockTransferDetailRepository $stockTransferDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->stockTransferHeaderRepository = $entityManager->getRepository(StockTransferHeader::class);
        $this->stockTransferDetailRepository = $entityManager->getRepository(StockTransferDetail::class);
    }

    public function initialize(StockTransferHeader $stockTransferHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($stockTransferHeader->getId())) {
            $stockTransferHeader->setCreatedTransactionDateTime($datetime);
            $stockTransferHeader->setCreatedTransactionUser($user);
        } else {
            $stockTransferHeader->setModifiedTransactionDateTime($datetime);
            $stockTransferHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(StockTransferHeader $stockTransferHeader, array $options = []): void
    {
        if ($stockTransferHeader->getTransactionDate() !== null) {
            $year = $stockTransferHeader->getTransactionDate()->format('y');
            $month = $stockTransferHeader->getTransactionDate()->format('m');
            $lastStockTransferHeader = $this->stockTransferHeaderRepository->findRecentBy($year, $month);
            $currentStockTransferHeader = ($lastStockTransferHeader === null) ? $stockTransferHeader : $lastStockTransferHeader;
            $stockTransferHeader->setCodeNumberToNext($currentStockTransferHeader->getCodeNumber(), $year, $month);

        }
        foreach ($stockTransferHeader->getStockTransferDetails() as $stockTransferDetail) {
            $stockTransferDetail->setIsCanceled($stockTransferDetail->getSyncIsCanceled());
        }
        $stockTransferHeader->setTotalQuantity($stockTransferHeader->getSyncTotalQuantity());
    }

    public function save(StockTransferHeader $stockTransferHeader, array $options = []): void
    {
        $this->stockTransferHeaderRepository->add($stockTransferHeader);
        foreach ($stockTransferHeader->getStockTransferDetails() as $stockTransferDetail) {
            $this->stockTransferDetailRepository->add($stockTransferDetail);
        }
        $this->entityManager->flush();
    }
}
