<?php

namespace App\Service\Transaction;

use App\Entity\Transaction\ReceiveDetail;
use App\Entity\Transaction\ReceiveHeader;
use App\Repository\Transaction\ReceiveDetailRepository;
use App\Repository\Transaction\ReceiveHeaderRepository;
use Doctrine\ORM\EntityManagerInterface;

class ReceiveHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private ReceiveHeaderRepository $receiveHeaderRepository;
    private ReceiveDetailRepository $receiveDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->receiveHeaderRepository = $entityManager->getRepository(ReceiveHeader::class);
        $this->receiveDetailRepository = $entityManager->getRepository(ReceiveDetail::class);
    }

    public function initialize(ReceiveHeader $receiveHeader, array $options = []): void
    {
//        list($month, $year, $staff) = array($params['month'], $params['year'], $params['staff']);
        
        if (empty($receiveHeader->getId())) {
//            $lastReceiveHeader = $this->receiveHeaderRepository->findRecentBy($year, $month);
//            $currentReceiveHeader = ($lastReceiveHeader === null) ? $receiveHeader : $lastReceiveHeader;
//            $receiveHeader->setCodeNumberToNext($currentReceiveHeader->getCodeNumber(), $year, $month);
            
//            $receiveHeader->setStaffCreated($staff);
//            $receiveHeader->setTotalPayment(0.00);
//            $receiveHeader->setTotalReturn(0.00);
        }
    }

    public function finalize(ReceiveHeader $receiveHeader, array $options = []): void
    {
//        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
//            $receiveDetail->setReceiveHeader($receiveHeader);
//        }
        $this->sync($receiveHeader);
    }

    public function save(ReceiveHeader $receiveHeader, array $options = []): void
    {
        $this->receiveHeaderRepository->add($receiveHeader);
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $this->receiveDetailRepository->add($receiveDetail);
        }
        $this->entityManager->flush();
    }

    public function sync(ReceiveHeader $receiveHeader): void
    {
        foreach ($receiveHeader->getReceiveDetails() as $receiveDetail) {
            $receiveDetail->sync();
        }
        $receiveHeader->sync();
    }
}
