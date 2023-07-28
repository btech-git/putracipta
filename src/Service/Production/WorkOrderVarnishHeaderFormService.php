<?php

namespace App\Service\Production;

use App\Entity\Production\WorkOrderVarnishHeader;
use App\Repository\Production\WorkOrderVarnishHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkOrderVarnishHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderVarnishHeaderRepository $workOrderVarnishHeaderRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->workOrderVarnishHeaderRepository = $entityManager->getRepository(WorkOrderVarnishHeader::class);
    }

    public function initialize(WorkOrderVarnishHeader $workOrderVarnishHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($workOrderVarnishHeader->getId())) {
            $workOrderVarnishHeader->setCreatedTransactionDateTime($datetime);
            $workOrderVarnishHeader->setCreatedTransactionUser($user);
        } else {
            $workOrderVarnishHeader->setModifiedTransactionDateTime($datetime);
            $workOrderVarnishHeader->setModifiedTransactionUser($user);
        }
    }

    public function finalize(WorkOrderVarnishHeader $workOrderVarnishHeader, array $options = []): void
    {
        if ($workOrderVarnishHeader->getTransactionDate() !== null) {
            $year = $workOrderVarnishHeader->getTransactionDate()->format('y');
            $month = $workOrderVarnishHeader->getTransactionDate()->format('m');
            $lastWorkOrderVarnishHeader = $this->workOrderVarnishHeaderRepository->findRecentBy($year, $month);
            $currentWorkOrderVarnishHeader = ($lastWorkOrderVarnishHeader === null) ? $workOrderVarnishHeader : $lastWorkOrderVarnishHeader;
            $workOrderVarnishHeader->setCodeNumberToNext($currentWorkOrderVarnishHeader->getCodeNumber(), $year, $month);

        }
        
    }

    public function save(WorkOrderVarnishHeader $workOrderVarnishHeader, array $options = []): void
    {
        $this->workOrderVarnishHeaderRepository->add($workOrderVarnishHeader);
        $this->entityManager->flush();
    }
}
