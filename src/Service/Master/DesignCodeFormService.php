<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DesignCode;
use App\Entity\Master\DesignCodeCheckSheetDetail;
use App\Entity\Master\DesignCodeDistributionDetail;
use App\Entity\Master\DesignCodeProcessDetail;
use App\Entity\Support\Idempotent;
use App\Repository\Master\DesignCodeRepository;
use App\Repository\Master\DesignCodeCheckSheetDetailRepository;
use App\Repository\Master\DesignCodeDistributionDetailRepository;
use App\Repository\Master\DesignCodeProcessDetailRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DesignCodeFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private DesignCodeRepository $designCodeRepository;
    private DesignCodeCheckSheetDetailRepository $designCodeCheckSheetDetailRepository;
    private DesignCodeDistributionDetailRepository $designCodeDistributionDetailRepository;
    private DesignCodeProcessDetailRepository $designCodeProcessDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->designCodeRepository = $entityManager->getRepository(DesignCode::class);
        $this->designCodeCheckSheetDetailRepository = $entityManager->getRepository(DesignCodeCheckSheetDetail::class);
        $this->designCodeDistributionDetailRepository = $entityManager->getRepository(DesignCodeDistributionDetail::class);
        $this->designCodeProcessDetailRepository = $entityManager->getRepository(DesignCodeProcessDetail::class);
    }

    public function save(DesignCode $designCode, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->designCodeRepository->add($designCode);
        foreach ($designCode->getDesignCodeProcessDetails() as $designCodeProcessDetail) {
            $this->designCodeProcessDetailRepository->add($designCodeProcessDetail);
        }
        foreach ($designCode->getDesignCodeDistributionDetails() as $designCodeDistributionDetail) {
            $this->designCodeDistributionDetailRepository->add($designCodeDistributionDetail);
        }
        foreach ($designCode->getDesignCodeCheckSheetDetails() as $designCodeCheckSheetDetail) {
            $this->designCodeCheckSheetDetailRepository->add($designCodeCheckSheetDetail);
        }
        $this->entityManager->flush();
    }

    public function copyFrom(DesignCode $sourceDesignCode): DesignCode
    {
        $designCode = new DesignCode();
        $designCode->setName($sourceDesignCode->getName());
        $designCode->setVariant($sourceDesignCode->getVariant());
        $designCode->setCustomer($sourceDesignCode->getCustomer());
        $designCode->setColor($sourceDesignCode->getColor());
        $designCode->setPantone($sourceDesignCode->getPantone());
        $designCode->setCoating($sourceDesignCode->getCoating());
        $designCode->setCode($sourceDesignCode->getCode());
        $designCode->setColorSpecial1($sourceDesignCode->getColorSpecial1());
        $designCode->setColorSpecial2($sourceDesignCode->getColorSpecial2());
        $designCode->setColorSpecial3($sourceDesignCode->getColorSpecial3());
        $designCode->setColorSpecial4($sourceDesignCode->getColorSpecial4());
        $designCode->setPrintingUpQuantity($sourceDesignCode->getPrintingUpQuantity());
        $designCode->setPrintingKrisSize($sourceDesignCode->getPrintingKrisSize());
        $designCode->setPaperMountage($sourceDesignCode->getPaperMountage());
        $designCode->setDiecutKnife($sourceDesignCode->getDiecutKnife());
        $designCode->setDielineMillar($sourceDesignCode->getDielineMillar());
        $designCode->setStatus($sourceDesignCode->getStatus());
        $designCode->setPaperCuttingLength($sourceDesignCode->getPaperCuttingLength());
        $designCode->setPaperCuttingWidth($sourceDesignCode->getPaperCuttingWidth());
        $designCode->setInkCyanPercentage($sourceDesignCode->getInkCyanPercentage());
        $designCode->setInkMagentaPercentage($sourceDesignCode->getInkMagentaPercentage());
        $designCode->setInkYellowPercentage($sourceDesignCode->getInkYellowPercentage());
        $designCode->setInkBlackPercentage($sourceDesignCode->getInkBlackPercentage());
        $designCode->setInkOpvPercentage($sourceDesignCode->getInkOpvPercentage());
        $designCode->setInkK1Percentage($sourceDesignCode->getInkK1Percentage());
        $designCode->setInkK2Percentage($sourceDesignCode->getInkK2Percentage());
        $designCode->setInkK3Percentage($sourceDesignCode->getInkK3Percentage());
        $designCode->setInkK4Percentage($sourceDesignCode->getInkK4Percentage());
        $designCode->setPackagingGlueQuantity($sourceDesignCode->getPackagingGlueQuantity());
        $designCode->setPackagingRubberQuantity($sourceDesignCode->getPackagingRubberQuantity());
        $designCode->setPackagingPaperQuantity($sourceDesignCode->getPackagingPaperQuantity());
        $designCode->setPackagingBoxQuantity($sourceDesignCode->getPackagingBoxQuantity());
        $designCode->setPackagingTapeLargeQuantity($sourceDesignCode->getPackagingTapeLargeQuantity());
        $designCode->setPackagingTapeSmallQuantity($sourceDesignCode->getPackagingTapeSmallQuantity());
        $designCode->setPackagingPlasticQuantity($sourceDesignCode->getPackagingPlasticQuantity());
        $designCode->setInsitPrintingPercentage($sourceDesignCode->getInsitPrintingPercentage());
        $designCode->setInsitSortingPercentage($sourceDesignCode->getInsitSortingPercentage());
        $designCode->setHotStamping($sourceDesignCode->getHotStamping());
        foreach ($sourceDesignCode->getDesignCodeCheckSheetDetails() as $sourceDesignCodeCheckSheetDetail) {
            $designCodeCheckSheetDetail = new DesignCodeProcessDetail();
            $designCodeCheckSheetDetail->setDesignCode($sourceDesignCodeCheckSheetDetail->getDesignCode());
            $designCodeCheckSheetDetail->setWorkOrderCheckSheet($sourceDesignCodeCheckSheetDetail->getWorkOrderCheckSheet());
            $designCode->addDesignCodeCheckSheetDetail($designCodeCheckSheetDetail);
        }
        foreach ($sourceDesignCode->getDesignCodeDistributionDetails() as $sourceDesignCodeDistributionDetail) {
            $designCodeDistributionDetail = new DesignCodeDistributionDetail();
            $designCodeDistributionDetail->setDesignCode($sourceDesignCodeDistributionDetail->getDesignCode());
            $designCodeDistributionDetail->setWorkOrderDistribution($sourceDesignCodeDistributionDetail->getWorkOrderDistribution());
            $designCode->addDesignCodeDistributionDetail($sourceDesignCodeDistributionDetail);
        }
        foreach ($sourceDesignCode->getDesignCodeProcessDetails() as $sourceDesignCodeProcessDetail) {
            $designCodeProcessDetail = new DesignCodeProcessDetail();
            $designCodeProcessDetail->setDesignCode($sourceDesignCodeProcessDetail->getDesignCode());
            $designCodeProcessDetail->setWorkOrderProcess($sourceDesignCodeProcessDetail->getWorkOrderProcess());
            $designCode->addDesignCodeProcessDetail($designCodeProcessDetail);
        }
        return $designCode;
    }
}
