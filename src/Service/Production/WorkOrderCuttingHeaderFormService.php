<?php

namespace App\Service\Production;

use App\Entity\Production\WorkOrderCuttingHeader;
use App\Repository\Production\WorkOrderCuttingHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkOrderCuttingHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderCuttingHeaderRepository $workOrderCuttingHeaderRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->workOrderCuttingHeaderRepository = $entityManager->getRepository(WorkOrderCuttingHeader::class);
    }

    public function initialize(WorkOrderCuttingHeader $workOrderCuttingHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($workOrderCuttingHeader->getId())) {
            $workOrderCuttingHeader->setCreatedProductionDateTime($datetime);
            $workOrderCuttingHeader->setCreatedProductionUser($user);
        } else {
            $workOrderCuttingHeader->setModifiedProductionDateTime($datetime);
            $workOrderCuttingHeader->setModifiedProductionUser($user);
        }
    }

    public function finalize(WorkOrderCuttingHeader $workOrderCuttingHeader, array $options = []): void
    {
        if ($workOrderCuttingHeader->getTransactionDate() !== null) {
            $year = $workOrderCuttingHeader->getTransactionDate()->format('y');
            $month = $workOrderCuttingHeader->getTransactionDate()->format('m');
            $lastWorkOrderCuttingHeader = $this->workOrderCuttingHeaderRepository->findRecentBy($year, $month);
            $currentWorkOrderCuttingHeader = ($lastWorkOrderCuttingHeader === null) ? $workOrderCuttingHeader : $lastWorkOrderCuttingHeader;
            $workOrderCuttingHeader->setCodeNumberToNext($currentWorkOrderCuttingHeader->getCodeNumber(), $year, $month);

        }
        
    }

    public function save(WorkOrderCuttingHeader $workOrderCuttingHeader, array $options = []): void
    {
        $this->workOrderCuttingHeaderRepository->add($workOrderCuttingHeader);
        $this->entityManager->flush();
    }
}
