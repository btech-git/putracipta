<?php

namespace App\Service\Stock;

use App\Entity\Stock\MaterialReleaseDetail;
use App\Entity\Stock\MaterialReleaseHeader;
use App\Repository\Stock\MaterialReleaseDetailRepository;
use App\Repository\Stock\MaterialReleaseHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class MaterialReleaseHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private MaterialReleaseHeaderRepository $materialReleaseHeaderRepository;
    private MaterialReleaseDetailRepository $materialReleaseDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->materialReleaseHeaderRepository = $entityManager->getRepository(MaterialReleaseHeader::class);
        $this->materialReleaseDetailRepository = $entityManager->getRepository(MaterialReleaseDetail::class);
    }

    public function initialize(MaterialReleaseHeader $materialReleaseHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($materialReleaseHeader->getId())) {
            $materialReleaseHeader->setCreatedTransactionDateTime($datetime);
            $materialReleaseHeader->setCreatedTransactionUser($user);
        } else {
            $materialReleaseHeader->setModifiedTransactionDateTime($datetime);
            $materialReleaseHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(MaterialReleaseHeader $materialReleaseHeader, array $options = []): void
    {
        if ($materialReleaseHeader->getTransactionDate() !== null) {
            $year = $materialReleaseHeader->getTransactionDate()->format('y');
            $month = $materialReleaseHeader->getTransactionDate()->format('m');
            $lastMaterialReleaseHeader = $this->materialReleaseHeaderRepository->findRecentBy($year, $month);
            $currentMaterialReleaseHeader = ($lastMaterialReleaseHeader === null) ? $materialReleaseHeader : $lastMaterialReleaseHeader;
            $materialReleaseHeader->setCodeNumberToNext($currentMaterialReleaseHeader->getCodeNumber(), $year, $month);

        }
        $materialRequestHeader = $materialReleaseHeader->getMaterialRequestHeader();
        if ($materialRequestHeader !== null) {
            $materialReleaseHeader->setDepartmentName($materialRequestHeader->getDepartmentName());
            $materialReleaseHeader->setWorkOrderNumber($materialRequestHeader->getWorkOrderNumber());
            $materialReleaseHeader->setPartNumber($materialRequestHeader->getPartNumber());
        }
        
        foreach ($materialReleaseHeader->getMaterialReleaseDetails() as $materialReleaseDetail) {
            $materialReleaseDetail->setIsCanceled($materialReleaseDetail->getSyncIsCanceled());
            
            $materialRequestDetail = $materialReleaseDetail->getMaterialRequestDetail();
            $materialRequestHeader = $materialRequestDetail->getMaterialRequestHeader();
            $oldMaterialReleaseDetails = $this->materialReleaseDetailRepository->findByMaterialRequestDetail($materialRequestDetail);
            $totalRelease = 0;
            foreach ($oldMaterialReleaseDetails as $oldMaterialReleaseDetail) {
                if ($oldMaterialReleaseDetail->getId() !== $materialReleaseDetail->getId()) {
                    $totalRelease += $oldMaterialReleaseDetail->getQuantityRelease();
                }
            }
            $totalRelease += $materialReleaseDetail->getQuantity();
            $materialRequestDetail->setQuantityRelease($totalRelease);
            $materialRequestDetail->setQuantityRemaining($materialRequestDetail->getSyncQuantityRemaining());
            
            $materialReleaseDetail->setMaterial($materialRequestDetail->getMaterial());
            $materialRequestHeader->setTotalQuantityRemaining($materialRequestHeader->getSyncTotalQuantityRemaining());
            
        }
        
        $materialReleaseHeader->setTotalQuantity($materialReleaseHeader->getSyncTotalQuantity());
    }

    public function save(MaterialReleaseHeader $materialReleaseHeader, array $options = []): void
    {
        $this->materialReleaseHeaderRepository->add($materialReleaseHeader);
        foreach ($materialReleaseHeader->getMaterialReleaseDetails() as $materialReleaseDetail) {
            $this->materialReleaseDetailRepository->add($materialReleaseDetail);
        }
        $this->entityManager->flush();
    }
}
