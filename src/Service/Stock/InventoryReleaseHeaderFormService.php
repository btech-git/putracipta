<?php

namespace App\Service\Stock;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderPaperDetail;
use App\Entity\Stock\Inventory;
use App\Entity\Stock\InventoryReleaseMaterialDetail;
use App\Entity\Stock\InventoryReleasePaperDetail;
use App\Entity\Stock\InventoryReleaseHeader;
use App\Entity\Support\Idempotent;
use App\Repository\Purchase\PurchaseOrderDetailRepository;
use App\Repository\Purchase\PurchaseOrderPaperDetailRepository;
use App\Repository\Stock\InventoryReleaseMaterialDetailRepository;
use App\Repository\Stock\InventoryReleasePaperDetailRepository;
use App\Repository\Stock\InventoryReleaseHeaderRepository;
use App\Repository\Stock\InventoryRepository;
use App\Util\Service\InventoryUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class InventoryReleaseHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private InventoryReleaseHeaderRepository $inventoryReleaseHeaderRepository;
    private InventoryReleaseMaterialDetailRepository $inventoryReleaseMaterialDetailRepository;
    private InventoryReleasePaperDetailRepository $inventoryReleasePaperDetailRepository;
    private PurchaseOrderDetailRepository $purchaseOrderDetailRepository;
    private PurchaseOrderPaperDetailRepository $purchaseOrderPaperDetailRepository;
    private InventoryRepository $inventoryRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->inventoryReleaseHeaderRepository = $entityManager->getRepository(InventoryReleaseHeader::class);
        $this->inventoryReleaseMaterialDetailRepository = $entityManager->getRepository(InventoryReleaseMaterialDetail::class);
        $this->inventoryReleasePaperDetailRepository = $entityManager->getRepository(InventoryReleasePaperDetail::class);
        $this->purchaseOrderDetailRepository = $entityManager->getRepository(PurchaseOrderDetail::class);
        $this->purchaseOrderPaperDetailRepository = $entityManager->getRepository(PurchaseOrderPaperDetail::class);
        $this->inventoryRepository = $entityManager->getRepository(Inventory::class);
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
            $inventoryRequestMaterialDetail->setQuantityRemaining($inventoryRequestMaterialDetail->getSyncQuantityRemaining());
            
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
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->inventoryReleaseHeaderRepository->add($inventoryReleaseHeader);
        foreach ($inventoryReleaseHeader->getInventoryReleaseMaterialDetails() as $inventoryReleaseMaterialDetail) {
            $this->inventoryReleaseMaterialDetailRepository->add($inventoryReleaseMaterialDetail);
        }
        foreach ($inventoryReleaseHeader->getInventoryReleasePaperDetails() as $inventoryReleasePaperDetail) {
            $this->inventoryReleasePaperDetailRepository->add($inventoryReleasePaperDetail);
        }
        $this->addInventories($inventoryReleaseHeader);
        $this->entityManager->flush();
    }

    private function addInventories(InventoryReleaseHeader $inventoryReleaseHeader): void
    {
        InventoryUtil::reverseOldData($this->inventoryRepository, $inventoryReleaseHeader);
        if ($inventoryReleaseHeader->getReleaseMode() === InventoryReleaseHeader::RELEASE_MODE_MATERIAL) {
            $inventoryReleaseMaterialDetails = $inventoryReleaseHeader->getInventoryReleaseMaterialDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('material', $this->purchaseOrderDetailRepository, $inventoryReleaseMaterialDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $inventoryReleaseHeader, $inventoryReleaseMaterialDetails, function($newInventory, $inventoryReleaseMaterialDetail) use ($averagePriceList, $inventoryReleaseHeader) {
                $material = $inventoryReleaseMaterialDetail->getMaterial();
                $purchasePrice = isset($averagePriceList[$material->getId()]) ? $averagePriceList[$material->getId()] : '0.00';
                $newInventory->setTransactionSubject($inventoryReleaseMaterialDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setMaterial($material);
                $newInventory->setWarehouse($inventoryReleaseHeader->getWarehouse());
                $newInventory->setInventoryMode($inventoryReleaseHeader->getReleaseMode());
                $newInventory->setQuantityIn($inventoryReleaseMaterialDetail->getQuantity());
            });
        } else if ($inventoryReleaseHeader->getReleaseMode() === InventoryReleaseHeader::RELEASE_MODE_PAPER) {
            $inventoryReleasePaperDetails = $inventoryReleaseHeader->getInventoryReleasePaperDetails()->toArray();
            $averagePriceList = InventoryUtil::getAveragePriceList('paper', $this->purchaseOrderPaperDetailRepository, $inventoryReleasePaperDetails);
            InventoryUtil::addNewData($this->inventoryRepository, $inventoryReleaseHeader, $inventoryReleasePaperDetails, function($newInventory, $inventoryReleasePaperDetail) use ($averagePriceList, $inventoryReleaseHeader) {
                $paper = $inventoryReleasePaperDetail->getPaper();
                $purchasePrice = isset($averagePriceList[$paper->getId()]) ? $averagePriceList[$paper->getId()] : '0.00';
                $newInventory->setTransactionSubject($inventoryReleasePaperDetail->getMemo());
                $newInventory->setPurchasePrice($purchasePrice);
                $newInventory->setPaper($paper);
                $newInventory->setWarehouse($inventoryReleaseHeader->getWarehouse());
                $newInventory->setInventoryMode($inventoryReleaseHeader->getReleaseMode());
                $newInventory->setQuantityIn($inventoryReleasePaperDetail->getQuantity());
            });
        }
    }
}
