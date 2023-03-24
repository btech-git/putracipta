<?php

namespace App\Service\Production;

use App\Entity\Production\WorkOrderHeader;
use App\Repository\Production\WorkOrderHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkOrderHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderHeaderRepository $workOrderHeaderRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->workOrderHeaderRepository = $entityManager->getRepository(WorkOrderHeader::class);
    }

    public function initialize(WorkOrderHeader $workOrderHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($workOrderHeader->getId())) {
            $workOrderHeader->setCreatedProductionDateTime($datetime);
            $workOrderHeader->setCreatedProductionUser($user);
        } else {
            $workOrderHeader->setModifiedProductionDateTime($datetime);
            $workOrderHeader->setModifiedProductionUser($user);
        }
    }

    public function finalize(WorkOrderHeader $workOrderHeader, array $options = []): void
    {
        if ($workOrderHeader->getTransactionDate() !== null) {
            $year = $workOrderHeader->getTransactionDate()->format('y');
            $month = $workOrderHeader->getTransactionDate()->format('m');
            $lastWorkOrderHeader = $this->workOrderHeaderRepository->findRecentBy($year, $month);
            $currentWorkOrderHeader = ($lastWorkOrderHeader === null) ? $workOrderHeader : $lastWorkOrderHeader;
            $workOrderHeader->setCodeNumberToNext($currentWorkOrderHeader->getCodeNumber(), $year, $month);

        }
        
    }

    public function save(WorkOrderHeader $workOrderHeader, array $options = []): void
    {
        $this->workOrderHeaderRepository->add($workOrderHeader);
        $this->entityManager->flush();
    }
}
