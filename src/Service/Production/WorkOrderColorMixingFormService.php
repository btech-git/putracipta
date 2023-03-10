<?php

namespace App\Service\Production;

use App\Entity\Production\WorkOrderColorMixing;
use App\Repository\Production\WorkOrderColorMixingRepository;
use Doctrine\ORM\EntityManagerInterface;

class WorkOrderColorMixingFormService
{
    private EntityManagerInterface $entityManager;
    private WorkOrderColorMixingRepository $workOrderColorMixingRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->workOrderColorMixingRepository = $entityManager->getRepository(WorkOrderColorMixing::class);
    }

    public function initialize(WorkOrderColorMixing $workOrderColorMixing, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($workOrderColorMixing->getId())) {
            $lastWorkOrderColorMixing = $this->workOrderColorMixingRepository->findRecentBy($year, $month);
            $currentWorkOrderColorMixing = ($lastWorkOrderColorMixing === null) ? $workOrderColorMixing : $lastWorkOrderColorMixing;
            $workOrderColorMixing->setCodeNumberToNext($currentWorkOrderColorMixing->getCodeNumber(), $year, $month);

            $workOrderColorMixing->setCreatedProductionDateTime($datetime);
            $workOrderColorMixing->setCreatedProductionUser($user);
        } else {
            $workOrderColorMixing->setModifiedProductionDateTime($datetime);
            $workOrderColorMixing->setModifiedProductionUser($user);
        }
    }

    public function finalize(WorkOrderColorMixing $workOrderColorMixing, array $options = []): void
    {
        
    }

    public function save(WorkOrderColorMixing $workOrderColorMixing, array $options = []): void
    {
        $this->workOrderColorMixingRepository->add($workOrderColorMixing);
        $this->entityManager->flush();
    }
}
