<?php

namespace App\Service\Production;

use App\Entity\Production\MasterOrderCheckSheetDetail;
use App\Entity\Production\MasterOrderDistributionDetail;
use App\Entity\Production\MasterOrderHeader;
use App\Entity\Production\MasterOrderProcessDetail;
use App\Entity\Production\MasterOrderProductDetail;
use App\Repository\Production\MasterOrderCheckSheetDetailRepository;
use App\Repository\Production\MasterOrderDistributionDetailRepository;
use App\Repository\Production\MasterOrderHeaderRepository;
use App\Repository\Production\MasterOrderProcessDetailRepository;
use App\Repository\Production\MasterOrderProductDetailRepository;
use Doctrine\ORM\EntityManagerInterface;

class MasterOrderHeaderFormService
{
    private EntityManagerInterface $entityManager;
    private MasterOrderHeaderRepository $masterOrderHeaderRepository;
    private MasterOrderProductDetailRepository $masterOrderProductDetailRepository;
    private MasterOrderProcessDetailRepository $masterOrderProcessDetailRepository;
    private MasterOrderDistributionDetailRepository $masterOrderDistributionDetailRepository;
    private MasterOrderCheckSheetDetailRepository $masterOrderCheckSheetDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->masterOrderHeaderRepository = $entityManager->getRepository(MasterOrderHeader::class);
        $this->masterOrderProductDetailRepository = $entityManager->getRepository(MasterOrderProductDetail::class);
        $this->masterOrderProcessDetailRepository = $entityManager->getRepository(MasterOrderProcessDetail::class);
        $this->masterOrderDistributionDetailRepository = $entityManager->getRepository(MasterOrderDistributionDetail::class);
        $this->masterOrderCheckSheetDetailRepository = $entityManager->getRepository(MasterOrderCheckSheetDetail::class);
    }

    public function initialize(MasterOrderHeader $masterOrderHeader, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($masterOrderHeader->getId())) {
            $masterOrderHeader->setCreatedProductionDateTime($datetime);
            $masterOrderHeader->setCreatedProductionUser($user);
        } else {
            $masterOrderHeader->setModifiedProductionDateTime($datetime);
            $masterOrderHeader->setModifiedProductionUser($user);
        }
    }

    public function finalize(MasterOrderHeader $masterOrderHeader, array $options = []): void
    {
        if ($masterOrderHeader->getProductionDate() !== null) {
            $year = $masterOrderHeader->getProductionDate()->format('y');
            $month = $masterOrderHeader->getProductionDate()->format('m');
            $lastMasterOrderHeader = $this->masterOrderHeaderRepository->findRecentBy($year, $month);
            $currentMasterOrderHeader = ($lastMasterOrderHeader === null) ? $masterOrderHeader : $lastMasterOrderHeader;
            $masterOrderHeader->setCodeNumberToNext($currentMasterOrderHeader->getCodeNumber(), $year, $month);
        }
        
        foreach ($masterOrderHeader->getMasterOrderProductDetails() as $masterOrderProductDetail) {
            $saleOrderDetail = $masterOrderProductDetail->getSaleOrderDetail();
            $masterOrderProductDetail->setProduct($saleOrderDetail->getProduct());
            $masterOrderProductDetail->setQuantityOrder($saleOrderDetail->getQuantity());
//            $masterOrderProductDetail->setQuantityStock(1000);
            $masterOrderProductDetail->setQuantityShortage($masterOrderProductDetail->getSyncQuantityShortage());
        }
        $masterOrderHeader->setTotalQuantityOrder($masterOrderHeader->getSyncTotalQuantityOrder());
        $masterOrderHeader->setTotalQuantityStock($masterOrderHeader->getSyncTotalQuantityStock());
        $masterOrderHeader->setTotalQuantityShortage($masterOrderHeader->getSyncTotalQuantityShortage());
        
        $paper = $masterOrderHeader->getPaper();
        if (!empty($paper)) {
            $masterOrderHeader->setPaperPlanoLength($paper->getLength());
            $masterOrderHeader->setPaperPlanoWidth($paper->getWidth());
        }
        $masterOrderHeader->setQuantityPaper($masterOrderHeader->getSyncQuantityPaper());
        $masterOrderHeader->setPaperTotal($masterOrderHeader->getSyncPaperTotal());
        $masterOrderHeader->setInsitPrintingQuantity($masterOrderHeader->getSyncInsitPrintingQuantity());
        $masterOrderHeader->setInsitSortingQuantity($masterOrderHeader->getSyncInsitSortingQuantity());
        $masterOrderHeader->setPaperRequirement($masterOrderHeader->getSyncPaperRequirement());
        $masterOrderHeader->setInkCyanWeight($masterOrderHeader->getSyncInkCyanWeight());
        $masterOrderHeader->setInkMagentaWeight($masterOrderHeader->getSyncInkMagentaWeight());
        $masterOrderHeader->setInkYellowWeight($masterOrderHeader->getSyncInkYellowWeight());
        $masterOrderHeader->setInkBlackWeight($masterOrderHeader->getSyncInkBlackWeight());
        $masterOrderHeader->setInkOpvWeight($masterOrderHeader->getSyncInkOpvWeight());
        $masterOrderHeader->setInkK1Weight($masterOrderHeader->getSyncInkK1Weight());
        $masterOrderHeader->setInkK2Weight($masterOrderHeader->getSyncInkK2Weight());
        $masterOrderHeader->setInkK3Weight($masterOrderHeader->getSyncInkK3Weight());
        $masterOrderHeader->setInkK4Weight($masterOrderHeader->getSyncInkK4Weight());
        $masterOrderHeader->setInkLaminatingQuantity($masterOrderHeader->getSyncInkLaminatingQuantity());
        $masterOrderHeader->setPackagingGlueWeight($masterOrderHeader->getSyncPackagingGlueWeight());
        $masterOrderHeader->setPackagingRubberWeight($masterOrderHeader->getSyncPackagingRubberWeight());
        $masterOrderHeader->setPackagingPaperWeight($masterOrderHeader->getSyncPackagingPaperWeight());
        $masterOrderHeader->setPackagingBoxWeight($masterOrderHeader->getSyncPackagingBoxWeight());
        $masterOrderHeader->setPackagingTapeLargeSize($masterOrderHeader->getSyncPackagingTapeLargeSize());
        $masterOrderHeader->setPackagingTapeSmallSize($masterOrderHeader->getSyncPackagingTapeSmallSize());
        $masterOrderHeader->setPackagingPlasticSize($masterOrderHeader->getSyncPackagingPlasticSize());
        
        if ($options['transactionFile']) {
            $masterOrderHeader->setLayoutModelFileExtension($options['transactionFile']->guessExtension());
        }
    }

    public function save(MasterOrderHeader $masterOrderHeader, array $options = []): void
    {
        $this->masterOrderHeaderRepository->add($masterOrderHeader);
        foreach ($masterOrderHeader->getMasterOrderProductDetails() as $masterOrderProductDetail) {
            $this->masterOrderProductDetailRepository->add($masterOrderProductDetail);
        }
        foreach ($masterOrderHeader->getMasterOrderDistributionDetails() as $masterOrderDistributionDetail) {
            $this->masterOrderDistributionDetailRepository->add($masterOrderDistributionDetail);
        }
        foreach ($masterOrderHeader->getMasterOrderProcessDetails() as $masterOrderProcessDetail) {
            $this->masterOrderProcessDetailRepository->add($masterOrderProcessDetail);
        }
        foreach ($masterOrderHeader->getMasterOrderCheckSheetDetails() as $masterOrderCheckSheetDetail) {
            $this->masterOrderCheckSheetDetailRepository->add($masterOrderCheckSheetDetail);
        }
        $this->entityManager->flush();
    }

    public function uploadFile(MasterOrderHeader $masterOrderHeader, $transactionFile, $uploadDirectory): void
    {
        if ($transactionFile) {
            try {
                $filename = $masterOrderHeader->getId() . '.' . $masterOrderHeader->getLayoutModelFileExtension();
                $transactionFile->move($uploadDirectory, $filename);
            } catch (FileException $e) {
            }
        }
    }
}
