<?php

namespace App\Service\Stock;

use App\Entity\Stock\InventoryRequestMaterialDetail;
use App\Entity\Stock\InventoryRequestPaperDetail;
use App\Entity\Stock\InventoryRequestHeader;
use App\Repository\Stock\InventoryRequestMaterialDetailRepository;
use App\Repository\Stock\InventoryRequestPaperDetailRepository;
use App\Repository\Stock\InventoryRequestHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class InventoryRequestHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private InventoryRequestHeaderRepository $inventoryRequestHeaderRepository;
    private InventoryRequestMaterialDetailRepository $inventoryRequestMaterialDetailRepository;
    private InventoryRequestPaperDetailRepository $inventoryRequestPaperDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->inventoryRequestHeaderRepository = $entityManager->getRepository(InventoryRequestHeader::class);
        $this->inventoryRequestMaterialDetailRepository = $entityManager->getRepository(InventoryRequestMaterialDetail::class);
        $this->inventoryRequestPaperDetailRepository = $entityManager->getRepository(InventoryRequestPaperDetail::class);
    }

    public function initialize(InventoryRequestHeader $inventoryRequestHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($inventoryRequestHeader->getId())) {
            $inventoryRequestHeader->setCreatedTransactionDateTime($datetime);
            $inventoryRequestHeader->setCreatedTransactionUser($user);
        } else {
            $inventoryRequestHeader->setModifiedTransactionDateTime($datetime);
            $inventoryRequestHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(InventoryRequestHeader $inventoryRequestHeader, array $options = []): void
    {
        if ($inventoryRequestHeader->getTransactionDate() !== null) {
            $year = $inventoryRequestHeader->getTransactionDate()->format('y');
            $month = $inventoryRequestHeader->getTransactionDate()->format('m');
            $lastInventoryRequestHeader = $this->inventoryRequestHeaderRepository->findRecentBy($year, $month);
            $currentInventoryRequestHeader = ($lastInventoryRequestHeader === null) ? $inventoryRequestHeader : $lastInventoryRequestHeader;
            $inventoryRequestHeader->setCodeNumberToNext($currentInventoryRequestHeader->getCodeNumber(), $year, $month);

        }
        
        foreach ($inventoryRequestHeader->getInventoryRequestMaterialDetails() as $inventoryRequestMaterialDetail) {
            $inventoryRequestMaterialDetail->setIsCanceled($inventoryRequestMaterialDetail->getSyncIsCanceled());
        }
        foreach ($inventoryRequestHeader->getInventoryRequestPaperDetails() as $inventoryRequestPaperDetail) {
            $inventoryRequestPaperDetail->setIsCanceled($inventoryRequestPaperDetail->getSyncIsCanceled());
        }
        $inventoryRequestHeader->setTotalQuantity($inventoryRequestHeader->getSyncTotalQuantity());
    }

    public function save(InventoryRequestHeader $inventoryRequestHeader, array $options = []): void
    {
        $this->inventoryRequestHeaderRepository->add($inventoryRequestHeader);
        foreach ($inventoryRequestHeader->getInventoryRequestMaterialDetails() as $inventoryRequestMaterialDetail) {
            $this->inventoryRequestMaterialDetailRepository->add($inventoryRequestMaterialDetail);
        }
        foreach ($inventoryRequestHeader->getInventoryRequestPaperDetails() as $inventoryRequestPaperDetail) {
            $this->inventoryRequestPaperDetailRepository->add($inventoryRequestPaperDetail);
        }
        $this->entityManager->flush();
    }
}
