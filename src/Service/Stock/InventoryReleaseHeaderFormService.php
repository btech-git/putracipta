<?php

namespace App\Service\Stock;

use App\Entity\Stock\InventoryReleaseMaterialDetail;
use App\Entity\Stock\InventoryReleasePaperDetail;
use App\Entity\Stock\InventoryReleaseHeader;
use App\Repository\Stock\InventoryReleaseMaterialDetailRepository;
use App\Repository\Stock\InventoryReleasePaperDetailRepository;
use App\Repository\Stock\InventoryReleaseHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class InventoryReleaseHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private InventoryReleaseHeaderRepository $inventoryReleaseHeaderRepository;
    private InventoryReleaseMaterialDetailRepository $inventoryReleaseMaterialDetailRepository;
    private InventoryReleasePaperDetailRepository $inventoryReleasePaperDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->inventoryReleaseHeaderRepository = $entityManager->getRepository(InventoryReleaseHeader::class);
        $this->inventoryReleaseMaterialDetailRepository = $entityManager->getRepository(InventoryReleaseMaterialDetail::class);
        $this->inventoryReleasePaperDetailRepository = $entityManager->getRepository(InventoryReleasePaperDetail::class);
    }

    public function initialize(InventoryReleaseHeader $inventoryReleaseHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($inventoryReleaseHeader->getId())) {
            $inventoryReleaseHeader->setCreatedTransactionDateTime($datetime);
            $inventoryReleaseHeader->setCreatedTransactionUser($user);
        } else {
            $inventoryReleaseHeader->setModifiedTransactionDateTime($datetime);
            $inventoryReleaseHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(InventoryReleaseHeader $inventoryReleaseHeader, array $options = []): void
    {
        if ($inventoryReleaseHeader->getTransactionDate() !== null) {
            $year = $inventoryReleaseHeader->getTransactionDate()->format('y');
            $month = $inventoryReleaseHeader->getTransactionDate()->format('m');
            $lastInventoryReleaseHeader = $this->inventoryReleaseHeaderRepository->findRecentBy($year, $month);
            $currentInventoryReleaseHeader = ($lastInventoryReleaseHeader === null) ? $inventoryReleaseHeader : $lastInventoryReleaseHeader;
            $inventoryReleaseHeader->setCodeNumberToNext($currentInventoryReleaseHeader->getCodeNumber(), $year, $month);
        }
        
        $inventoryRequestHeader = $inventoryReleaseHeader->getInventoryRequestHeader();
        
        if (!empty($inventoryRequestHeader)) {
            $inventoryReleaseHeader->setReleaseMode($inventoryRequestHeader->getRequestMode());
            $inventoryReleaseHeader->setWorkOrderNumber($inventoryRequestHeader->getWorkOrderNumber());
            $inventoryReleaseHeader->setDepartmentName($inventoryRequestHeader->getDepartmentName());
            $inventoryReleaseHeader->setPartNumber($inventoryRequestHeader->getPartNumber());
        }
        
        foreach ($inventoryReleaseHeader->getInventoryReleaseMaterialDetails() as $inventoryReleaseMaterialDetail) {
            $inventoryRequestMaterialDetail = $inventoryReleaseMaterialDetail->getInventoryRequestMaterialDetail();
            $inventoryReleaseMaterialDetail->setIsCanceled($inventoryReleaseMaterialDetail->getSyncIsCanceled());
            $inventoryReleaseMaterialDetail->setUnit($inventoryRequestMaterialDetail->getUnit());
            
            $oldReleaseDetails = $this->inventoryReleaseMaterialDetailRepository->findByInventoryRequestMaterialDetail($inventoryRequestMaterialDetail);
            $totalRelease = 0;
            foreach ($oldReleaseDetails as $oldReleaseDetail) {
                if ($oldReleaseDetail->getId() !== $inventoryReleaseMaterialDetail->getId() && $oldReleaseDetail->isIsCanceled() === false) {
                    $totalRelease += $oldReleaseDetail->getQuantity();
                }
            }
            if ($inventoryReleaseMaterialDetail->isIsCanceled() === false) {
                $totalRelease += $inventoryReleaseMaterialDetail->getQuantity();
            }
            $inventoryRequestMaterialDetail->setQuantityRelease($totalRelease);
            $inventoryRequestMaterialDetail->setQuantityRemaining($inventoryRequestMaterialDetail->getSyncRemainingDelivery());
            
        }
        foreach ($inventoryReleaseHeader->getInventoryReleasePaperDetails() as $inventoryReleasePaperDetail) {
            $inventoryRequestPaperDetail = $inventoryReleasePaperDetail->getInventoryRequestPaperDetail();
            $inventoryReleasePaperDetail->setIsCanceled($inventoryReleasePaperDetail->getSyncIsCanceled());
            $inventoryReleasePaperDetail->setUnit($inventoryRequestPaperDetail->getUnit());
        }
        $inventoryReleaseHeader->setTotalQuantity($inventoryReleaseHeader->getSyncTotalQuantity());
        
        if (!empty($inventoryRequestHeader)) {
            $inventoryRequestHeader->setTotalQuantityRelease($inventoryRequestHeader->getSyncTotalQuantityRelease());
            $inventoryRequestHeader->setTotalQuantityRemaining($inventoryRequestHeader->getSyncTotalQuantityRemaining());
        }
    }

    public function save(InventoryReleaseHeader $inventoryReleaseHeader, array $options = []): void
    {
        $this->inventoryReleaseHeaderRepository->add($inventoryReleaseHeader);
        foreach ($inventoryReleaseHeader->getInventoryReleaseMaterialDetails() as $inventoryReleaseMaterialDetail) {
            $this->inventoryReleaseMaterialDetailRepository->add($inventoryReleaseMaterialDetail);
        }
        foreach ($inventoryReleaseHeader->getInventoryReleasePaperDetails() as $inventoryReleasePaperDetail) {
            $this->inventoryReleasePaperDetailRepository->add($inventoryReleasePaperDetail);
        }
        $this->entityManager->flush();
    }
}
