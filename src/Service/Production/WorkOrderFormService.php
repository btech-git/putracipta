<?php

namespace App\Service\Production;

use App\Entity\Production\WorkOrder;
use App\Repository\Production\WorkOrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkOrderFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderRepository $workOrderRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->workOrderRepository = $entityManager->getRepository(WorkOrder::class);
    }

    public function initialize(WorkOrder $workOrder, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($workOrder->getId())) {
            $lastWorkOrder = $this->workOrderRepository->findRecentBy($year, $month);
            $currentWorkOrder = ($lastWorkOrder === null) ? $workOrder : $lastWorkOrder;
            $workOrder->setCodeNumberToNext($currentWorkOrder->getCodeNumber(), $year, $month);

            $workOrder->setCreatedProductionDateTime($datetime);
            $workOrder->setCreatedProductionUser($user);
        } else {
            $workOrder->setModifiedProductionDateTime($datetime);
            $workOrder->setModifiedProductionUser($user);
        }
    }

    public function finalize(WorkOrder $workOrder, array $options = []): void
    {
        
    }

    public function save(WorkOrder $workOrder, array $options = []): void
    {
        $this->workOrderRepository->add($workOrder);
        $this->entityManager->flush();
    }
}
