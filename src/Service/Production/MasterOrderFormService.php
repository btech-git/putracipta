<?php

namespace App\Service\Production;

use App\Entity\Production\MasterOrder;
use App\Repository\Production\MasterOrderRepository;
use Doctrine\ORM\EntityManagerInterface;

class MasterOrderFormService
{
    private EntityManagerInterface $entityManager;
    private MasterOrderRepository $masterOrderRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->masterOrderRepository = $entityManager->getRepository(MasterOrder::class);
    }

    public function initialize(MasterOrder $masterOrder, array $options = []): void
    {
        list($year, $month, $datetime, $user) = [$options['year'], $options['month'], $options['datetime'], $options['user']];

        if (empty($masterOrder->getId())) {
            $lastMasterOrder = $this->masterOrderRepository->findRecentBy($year, $month);
            $currentMasterOrder = ($lastMasterOrder === null) ? $masterOrder : $lastMasterOrder;
            $masterOrder->setCodeNumberToNext($currentMasterOrder->getCodeNumber(), $year, $month);

            $masterOrder->setCreatedProductionDateTime($datetime);
            $masterOrder->setCreatedProductionUser($user);
        } else {
            $masterOrder->setModifiedProductionDateTime($datetime);
            $masterOrder->setModifiedProductionUser($user);
        }
    }

    public function finalize(MasterOrder $masterOrder, array $options = []): void
    {
        $saleOrderDetail = $masterOrder->getSaleOrderDetail();
        $saleOrderHeader = $saleOrderDetail === null ? null : $saleOrderDetail->getSaleOrderHeader();
        $masterOrder->setCustomer($saleOrderHeader === null ? null : $saleOrderHeader->getCustomer());
        $masterOrder->setProduct($saleOrderDetail === null ? null : $saleOrderDetail->getProduct());
        
    }

    public function save(MasterOrder $masterOrder, array $options = []): void
    {
        $this->masterOrderRepository->add($masterOrder);
        $this->entityManager->flush();
    }
}
