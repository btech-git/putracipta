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
            $workOrderVarnishHeader->setCreatedProductionDateTime($datetime);
            $workOrderVarnishHeader->setCreatedProductionUser($user);
        } else {
            $workOrderVarnishHeader->setModifiedProductionDateTime($datetime);
            $workOrderVarnishHeader->setModifiedProductionUser($user);
        }
    }

    public function finalize(WorkOrderVarnishHeader $workOrderVarnishHeader, array $options = []): void
    {
        if ($workOrderVarnishHeader->getProductionDate() !== null) {
            $year = $workOrderVarnishHeader->getProductionDate()->format('y');
            $month = $workOrderVarnishHeader->getProductionDate()->format('m');
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
