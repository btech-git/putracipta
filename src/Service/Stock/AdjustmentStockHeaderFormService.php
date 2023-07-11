<?php

namespace App\Service\Stock;

use App\Entity\Stock\AdjustmentStockMaterialDetail;
use App\Entity\Stock\AdjustmentStockPaperDetail;
use App\Entity\Stock\AdjustmentStockProductDetail;
use App\Entity\Stock\AdjustmentStockHeader;
use App\Repository\Stock\AdjustmentStockMaterialDetailRepository;
use App\Repository\Stock\AdjustmentStockPaperDetailRepository;
use App\Repository\Stock\AdjustmentStockProductDetailRepository;
use App\Repository\Stock\AdjustmentStockHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class AdjustmentStockHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private AdjustmentStockHeaderRepository $adjustmentStockHeaderRepository;
    private AdjustmentStockMaterialDetailRepository $adjustmentStockMaterialDetailRepository;
    private AdjustmentStockPaperDetailRepository $adjustmentStockPaperDetailRepository;
    private AdjustmentStockProductDetailRepository $adjustmentStockProductDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->adjustmentStockHeaderRepository = $entityManager->getRepository(AdjustmentStockHeader::class);
        $this->adjustmentStockMaterialDetailRepository = $entityManager->getRepository(AdjustmentStockMaterialDetail::class);
        $this->adjustmentStockPaperDetailRepository = $entityManager->getRepository(AdjustmentStockPaperDetail::class);
        $this->adjustmentStockProductDetailRepository = $entityManager->getRepository(AdjustmentStockProductDetail::class);
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
        
        foreach ($adjustmentStockHeader->getAdjustmentStockMaterialDetails() as $adjustmentStockMaterialDetail) {
            $adjustmentStockMaterialDetail->setIsCanceled($adjustmentStockMaterialDetail->getSyncIsCanceled());
            $adjustmentStockMaterialDetail->setQuantityDifference($adjustmentStockMaterialDetail->getSyncQuantityDifference());
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockPaperDetails() as $adjustmentStockPaperDetail) {
            $adjustmentStockPaperDetail->setIsCanceled($adjustmentStockPaperDetail->getSyncIsCanceled());
            $adjustmentStockPaperDetail->setQuantityDifference($adjustmentStockPaperDetail->getSyncQuantityDifference());
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockProductDetails() as $adjustmentStockProductDetail) {
            $adjustmentStockProductDetail->setIsCanceled($adjustmentStockProductDetail->getSyncIsCanceled());
            $adjustmentStockProductDetail->setQuantityDifference($adjustmentStockProductDetail->getSyncQuantityDifference());
        }
    }

    public function save(AdjustmentStockHeader $adjustmentStockHeader, array $options = []): void
    {
        $this->adjustmentStockHeaderRepository->add($adjustmentStockHeader);
        foreach ($adjustmentStockHeader->getAdjustmentStockMaterialDetails() as $adjustmentStockMaterialDetail) {
            $this->adjustmentStockMaterialDetailRepository->add($adjustmentStockMaterialDetail);
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockPaperDetails() as $adjustmentStockPaperDetail) {
            $this->adjustmentStockPaperDetailRepository->add($adjustmentStockPaperDetail);
        }
        foreach ($adjustmentStockHeader->getAdjustmentStockProductDetails() as $adjustmentStockProductDetail) {
            $this->adjustmentStockProductDetailRepository->add($adjustmentStockProductDetail);
        }
        $this->entityManager->flush();
    }
}
