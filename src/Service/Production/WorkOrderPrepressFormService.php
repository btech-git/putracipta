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
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($workOrderPrepress->getId())) {
            $workOrderPrepress->setCreatedProductionDateTime($datetime);
            $workOrderPrepress->setCreatedProductionUser($user);
        } else {
            $workOrderPrepress->setModifiedProductionDateTime($datetime);
            $workOrderPrepress->setModifiedProductionUser($user);
        }
    }

    public function finalize(WorkOrderPrepress $workOrderPrepress, array $options = []): void
    {
        if ($workOrderPrepress->getTransactionDate() !== null) {
            $year = $workOrderPrepress->getTransactionDate()->format('y');
            $month = $workOrderPrepress->getTransactionDate()->format('m');
            $lastWorkOrderPrepress = $this->workOrderPrepressRepository->findRecentBy($year, $month);
            $currentWorkOrderPrepress = ($lastWorkOrderPrepress === null) ? $workOrderPrepress : $lastWorkOrderPrepress;
            $workOrderPrepress->setCodeNumberToNext($currentWorkOrderPrepress->getCodeNumber(), $year, $month);

        }
        
    }

    public function save(WorkOrderPrepress $workOrderPrepress, array $options = []): void
    {
        $this->workOrderPrepressRepository->add($workOrderPrepress);
        $this->entityManager->flush();
    }
}
