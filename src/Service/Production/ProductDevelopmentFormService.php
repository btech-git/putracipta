<?php

namespace App\Service\Production;

use App\Entity\Production\ProductDevelopment;
use App\Repository\Production\ProductDevelopmentRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductDevelopmentFormService
{
    private EntityManagerInterface $entityManager;
    private ProductDevelopmentRepository $productDevelopmentRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->productDevelopmentRepository = $entityManager->getRepository(ProductDevelopment::class);
    }

    public function initialize(ProductDevelopment $productDevelopment, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($productDevelopment->getId())) {
            $productDevelopment->setCreatedProductionDateTime($datetime);
            $productDevelopment->setCreatedProductionUser($user);
        } else {
            $productDevelopment->setModifiedProductionDateTime($datetime);
            $productDevelopment->setModifiedProductionUser($user);
        }
    }

    public function finalize(ProductDevelopment $productDevelopment, array $options = []): void
    {
        if ($productDevelopment->getProductionDate() !== null) {
            $year = $productDevelopment->getProductionDate()->format('y');
            $month = $productDevelopment->getProductionDate()->format('m');
            $lastProductDevelopment = $this->productDevelopmentRepository->findRecentBy($year, $month);
            $currentProductDevelopment = ($lastProductDevelopment === null) ? $productDevelopment : $lastProductDevelopment;
            $productDevelopment->setCodeNumberToNext($currentProductDevelopment->getCodeNumber(), $year, $month);
        }
        
        $productPrototype = $productDevelopment->getProductPrototype();
        if ($productPrototype !== null) {
            $productDevelopment->setDevelopmentTypeList($productPrototype->getDevelopmentTypeList());
        }
//        if ($productDevelopment->getId() === null) {
//            $time = new \DateTime();
//            $productDevelopment->setEpArtWorkFileTime($time);
//            $productDevelopment->setEpCustomerReviewTime(date('H:i:s'));
//            $productDevelopment->setEpSubConDiecutTime(date('H:i:s'));
//            $productDevelopment->setEpDielineDevelopmentTime(date('H:i:s'));
//            $productDevelopment->setEpImageDeliveryProductionTime(date('H:i:s'));
//            $productDevelopment->setEpDiecutDeliveryProductionTime(date('H:i:s'));
//            $productDevelopment->setEpDielineDeliveryProductionTime(date('H:i:s'));
//            $productDevelopment->setFepArtworkFileTime(date('H:i:s'));
//            $productDevelopment->setFepCustomerReviewTime(date('H:i:s'));
//            $productDevelopment->setFepSubconDiecutTime(date('H:i:s'));
//            $productDevelopment->setFepDielineDevelopmentTime(date('H:i:s'));
//            $productDevelopment->setFepImageDeliveryProductionTime(date('H:i:s'));
//            $productDevelopment->setFepDiecutDeliveryProductionTime(date('H:i:s'));
//            $productDevelopment->setFepDielineDeliveryProductionTime(date('H:i:s'));
//            $productDevelopment->setPsArtworkFileTime(date('H:i:s'));
//            $productDevelopment->setPsCustomerReviewTime(date('H:i:s'));
//            $productDevelopment->setPsSubconDiecutTime(date('H:i:s'));
//            $productDevelopment->setPsDielineDevelopmentTime(date('H:i:s'));
//            $productDevelopment->setPsImageDeliveryProductionTime(date('H:i:s'));
//            $productDevelopment->setPsDiecutDeliveryProductionTime(date('H:i:s'));
//            $productDevelopment->setPsDielineDeliveryProductionTime(date('H:i:s'));
//        }
    }

    public function save(ProductDevelopment $productDevelopment, array $options = []): void
    {
        $this->productDevelopmentRepository->add($productDevelopment);
        $this->entityManager->flush();
    }
}
