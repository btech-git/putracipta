<?php

namespace App\Service\Production;

use App\Entity\Production\WorkOrderOffsetPrintingHeader;
use App\Repository\Production\WorkOrderOffsetPrintingHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkOrderOffsetPrintingHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderOffsetPrintingHeaderRepository $workOrderOffsetPrintingHeaderRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->workOrderOffsetPrintingHeaderRepository = $entityManager->getRepository(WorkOrderOffsetPrintingHeader::class);
    }

    public function initialize(WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($workOrderOffsetPrintingHeader->getId())) {
            $lastWorkOrderOffsetPrintingHeader = $this->workOrderOffsetPrintingHeaderRepository->findRecentBy($year, $month);
            $currentWorkOrderOffsetPrintingHeader = ($lastWorkOrderOffsetPrintingHeader === null) ? $workOrderOffsetPrintingHeader : $lastWorkOrderOffsetPrintingHeader;
            $workOrderOffsetPrintingHeader->setCodeNumberToNext($currentWorkOrderOffsetPrintingHeader->getCodeNumber(), $year, $month);

            $workOrderOffsetPrintingHeader->setCreatedProductionDateTime($datetime);
            $workOrderOffsetPrintingHeader->setCreatedProductionUser($user);
        } else {
            $workOrderOffsetPrintingHeader->setModifiedProductionDateTime($datetime);
            $workOrderOffsetPrintingHeader->setModifiedProductionUser($user);
        }
    }

    public function finalize(WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader, array $options = []): void
    {
        
    }

    public function save(WorkOrderOffsetPrintingHeader $workOrderOffsetPrintingHeader, array $options = []): void
    {
        $this->workOrderOffsetPrintingHeaderRepository->add($workOrderOffsetPrintingHeader);
        $this->entityManager->flush();
    }
}
