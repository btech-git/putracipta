<?php

namespace App\Service\Stock;

use App\Entity\Stock\MaterialRequestDetail;
use App\Entity\Stock\MaterialRequestHeader;
use App\Repository\Stock\MaterialRequestDetailRepository;
use App\Repository\Stock\MaterialRequestHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class MaterialRequestHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private MaterialRequestHeaderRepository $materialRequestHeaderRepository;
    private MaterialRequestDetailRepository $materialRequestDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->materialRequestHeaderRepository = $entityManager->getRepository(MaterialRequestHeader::class);
        $this->materialRequestDetailRepository = $entityManager->getRepository(MaterialRequestDetail::class);
    }

    public function initialize(MaterialRequestHeader $materialRequestHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($materialRequestHeader->getId())) {
            $lastMaterialRequestHeader = $this->materialRequestHeaderRepository->findRecentBy($year, $month);
            $currentMaterialRequestHeader = ($lastMaterialRequestHeader === null) ? $materialRequestHeader : $lastMaterialRequestHeader;
            $materialRequestHeader->setCodeNumberToNext($currentMaterialRequestHeader->getCodeNumber(), $year, $month);

            $materialRequestHeader->setCreatedTransactionDateTime($datetime);
            $materialRequestHeader->setCreatedTransactionUser($user);
        } else {
            $materialRequestHeader->setModifiedTransactionDateTime($datetime);
            $materialRequestHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(MaterialRequestHeader $materialRequestHeader, array $options = []): void
    {
        foreach ($materialRequestHeader->getMaterialRequestDetails() as $materialRequestDetail) {
            $materialRequestDetail->setIsCanceled($materialRequestDetail->getSyncIsCanceled());
            $materialRequestDetail->setQuantityRemaining($materialRequestDetail->getSyncQuantityRemaining());
        }
        $materialRequestHeader->setTotalQuantity($materialRequestHeader->getSyncTotalQuantity());
        $materialRequestHeader->setTotalQuantityRemaining($materialRequestHeader->getSyncTotalQuantityRemaining());
    }

    public function save(MaterialRequestHeader $materialRequestHeader, array $options = []): void
    {
        $this->materialRequestHeaderRepository->add($materialRequestHeader);
        foreach ($materialRequestHeader->getMaterialRequestDetails() as $materialRequestDetail) {
            $this->materialRequestDetailRepository->add($materialRequestDetail);
        }
        $this->entityManager->flush();
    }
}
