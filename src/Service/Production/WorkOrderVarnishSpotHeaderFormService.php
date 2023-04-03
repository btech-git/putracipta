<?php

namespace App\Service\Production;

use App\Entity\Production\WorkOrderVarnishSpotHeader;
use App\Repository\Production\WorkOrderVarnishSpotHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkOrderVarnishSpotHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderVarnishSpotHeaderRepository $workOrderVarnishSpotHeaderRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->workOrderVarnishSpotHeaderRepository = $entityManager->getRepository(WorkOrderVarnishSpotHeader::class);
    }

    public function initialize(WorkOrderVarnishSpotHeader $workOrderVarnishSpotHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($workOrderVarnishSpotHeader->getId())) {
            $workOrderVarnishSpotHeader->setCreatedProductionDateTime($datetime);
            $workOrderVarnishSpotHeader->setCreatedProductionUser($user);
        } else {
            $workOrderVarnishSpotHeader->setModifiedProductionDateTime($datetime);
            $workOrderVarnishSpotHeader->setModifiedProductionUser($user);
        }
    }

    public function finalize(WorkOrderVarnishSpotHeader $workOrderVarnishSpotHeader, array $options = []): void
    {
        if ($workOrderVarnishSpotHeader->getProductionDate() !== null) {
            $year = $workOrderVarnishSpotHeader->getProductionDate()->format('y');
            $month = $workOrderVarnishSpotHeader->getProductionDate()->format('m');
            $lastWorkOrderVarnishSpotHeader = $this->workOrderVarnishSpotHeaderRepository->findRecentBy($year, $month);
            $currentWorkOrderVarnishSpotHeader = ($lastWorkOrderVarnishSpotHeader === null) ? $workOrderVarnishSpotHeader : $lastWorkOrderVarnishSpotHeader;
            $workOrderVarnishSpotHeader->setCodeNumberToNext($currentWorkOrderVarnishSpotHeader->getCodeNumber(), $year, $month);

        }
        
    }

    public function save(WorkOrderVarnishSpotHeader $workOrderVarnishSpotHeader, array $options = []): void
    {
        $this->workOrderVarnishSpotHeaderRepository->add($workOrderVarnishSpotHeader);
        $this->entityManager->flush();
    }
}
