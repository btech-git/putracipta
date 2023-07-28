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
            $productDevelopment->setCreatedTransactionDateTime($datetime);
            $productDevelopment->setCreatedTransactionUser($user);
        } else {
            $productDevelopment->setModifiedTransactionDateTime($datetime);
            $productDevelopment->setModifiedTransactionUser($user);
        }
    }

    public function finalize(ProductDevelopment $productDevelopment, array $options = []): void
    {
        if ($productDevelopment->getTransactionDate() !== null) {
            $year = $productDevelopment->getTransactionDate()->format('y');
            $month = $productDevelopment->getTransactionDate()->format('m');
            $lastProductDevelopment = $this->productDevelopmentRepository->findRecentBy($year, $month);
            $currentProductDevelopment = ($lastProductDevelopment === null) ? $productDevelopment : $lastProductDevelopment;
            $productDevelopment->setCodeNumberToNext($currentProductDevelopment->getCodeNumber(), $year, $month);
        }
        
        $productPrototype = $productDevelopment->getProductPrototype();
        if ($productPrototype !== null) {
            $productDevelopment->setDevelopmentTypeList($productPrototype->getDevelopmentTypeList());
        }
        
        if ($options['transactionFile']) {
            $productDevelopment->setTransactionFileExtension($options['transactionFile']->guessExtension());
        }
    }

    public function save(ProductDevelopment $productDevelopment, array $options = []): void
    {
        $this->productDevelopmentRepository->add($productDevelopment);
        $this->entityManager->flush();
    }

    public function uploadFile(ProductDevelopment $productDevelopment, $transactionFile, $uploadDirectory): void
    {
        if ($transactionFile) {
            try {
                $filename = $productDevelopment->getId() . '.' . $productDevelopment->getTransactionFileExtension();
                $transactionFile->move($uploadDirectory, $filename);
            } catch (FileException $e) {
            }
        }
    }
}
