<?php

namespace App\Service\Stock;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Stock\StockTransferMaterialDetail;
use App\Entity\Stock\StockTransferPaperDetail;
use App\Entity\Stock\StockTransferProductDetail;
use App\Entity\Stock\StockTransferHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Stock\StockTransferMaterialDetailRepository;
use App\Repository\Stock\StockTransferPaperDetailRepository;
use App\Repository\Stock\StockTransferProductDetailRepository;
use App\Repository\Stock\StockTransferHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class StockTransferHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private StockTransferHeaderRepository $stockTransferHeaderRepository;
    private StockTransferMaterialDetailRepository $stockTransferMaterialDetailRepository;
    private StockTransferPaperDetailRepository $stockTransferPaperDetailRepository;
    private StockTransferProductDetailRepository $stockTransferProductDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->stockTransferHeaderRepository = $entityManager->getRepository(StockTransferHeader::class);
        $this->stockTransferMaterialDetailRepository = $entityManager->getRepository(StockTransferMaterialDetail::class);
        $this->stockTransferPaperDetailRepository = $entityManager->getRepository(StockTransferPaperDetail::class);
        $this->stockTransferProductDetailRepository = $entityManager->getRepository(StockTransferProductDetail::class);
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
        foreach ($stockTransferHeader->getStockTransferMaterialDetails() as $stockTransferMaterialDetail) {
            $stockTransferMaterialDetail->setIsCanceled($stockTransferMaterialDetail->getSyncIsCanceled());
        }
        foreach ($stockTransferHeader->getStockTransferPaperDetails() as $stockTransferPaperDetail) {
            $stockTransferPaperDetail->setIsCanceled($stockTransferPaperDetail->getSyncIsCanceled());
        }
        foreach ($stockTransferHeader->getStockTransferProductDetails() as $stockTransferProductDetail) {
            $stockTransferProductDetail->setIsCanceled($stockTransferProductDetail->getSyncIsCanceled());
        }
        $stockTransferHeader->setTotalQuantity($stockTransferHeader->getSyncTotalQuantity());
    }

    public function save(StockTransferHeader $stockTransferHeader, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->stockTransferHeaderRepository->add($stockTransferHeader);
        foreach ($stockTransferHeader->getStockTransferMaterialDetails() as $stockTransferMaterialDetail) {
            $this->stockTransferMaterialDetailRepository->add($stockTransferMaterialDetail);
        }
        foreach ($stockTransferHeader->getStockTransferPaperDetails() as $stockTransferPaperDetail) {
            $this->stockTransferPaperDetailRepository->add($stockTransferPaperDetail);
        }
        foreach ($stockTransferHeader->getStockTransferProductDetails() as $stockTransferProductDetail) {
            $this->stockTransferProductDetailRepository->add($stockTransferProductDetail);
        }
        $this->entityManager->flush();
    }
}
