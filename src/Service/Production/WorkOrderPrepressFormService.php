<?php

namespace App\Service\Production;

use App\Entity\Production\WorkOrderPrepress;
use App\Repository\Production\WorkOrderPrepressRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkOrderPrepressFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderPrepressRepository $workOrderPrepressRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->workOrderPrepressRepository = $entityManager->getRepository(WorkOrderPrepress::class);
    }

    public function initialize(WorkOrderPrepress $workOrderPrepress, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($workOrderPrepress->getId())) {
            $lastWorkOrderPrepress = $this->workOrderPrepressRepository->findRecentBy($year, $month);
            $currentWorkOrderPrepress = ($lastWorkOrderPrepress === null) ? $workOrderPrepress : $lastWorkOrderPrepress;
            $workOrderPrepress->setCodeNumberToNext($currentWorkOrderPrepress->getCodeNumber(), $year, $month);

            $workOrderPrepress->setCreatedProductionDateTime($datetime);
            $workOrderPrepress->setCreatedProductionUser($user);
        } else {
            $workOrderPrepress->setModifiedProductionDateTime($datetime);
            $workOrderPrepress->setModifiedProductionUser($user);
        }
    }

    public function finalize(WorkOrderPrepress $workOrderPrepress, array $options = []): void
    {
        
    }

    public function save(WorkOrderPrepress $workOrderPrepress, array $options = []): void
    {
        $this->workOrderPrepressRepository->add($workOrderPrepress);
        $this->entityManager->flush();
    }
}
