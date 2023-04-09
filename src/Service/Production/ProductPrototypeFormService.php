<?php

namespace App\Service\Production;

use App\Entity\Production\ProductPrototype;
use App\Repository\Production\ProductPrototypeRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductPrototypeFormService
{
    private EntityManagerInterface $entityManager;
    private ProductPrototypeRepository $productPrototypeRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->productPrototypeRepository = $entityManager->getRepository(ProductPrototype::class);
    }

    public function initialize(ProductPrototype $productPrototype, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($productPrototype->getId())) {
            $productPrototype->setCreatedProductionDateTime($datetime);
            $productPrototype->setCreatedProductionUser($user);
        } else {
            $productPrototype->setModifiedProductionDateTime($datetime);
            $productPrototype->setModifiedProductionUser($user);
        }
    }

    public function finalize(ProductPrototype $productPrototype, array $options = []): void
    {
        if ($productPrototype->getProductionDate() !== null) {
            $year = $productPrototype->getProductionDate()->format('y');
            $month = $productPrototype->getProductionDate()->format('m');
            $lastProductPrototype = $this->productPrototypeRepository->findRecentBy($year, $month);
            $currentProductPrototype = ($lastProductPrototype === null) ? $productPrototype : $lastProductPrototype;
            $productPrototype->setCodeNumberToNext($currentProductPrototype->getCodeNumber(), $year, $month);
        }
        
        if ($options['transactionFile']) {
            $productPrototype->setProductionFileExtension($options['transactionFile']->guessExtension());
        }
    }

    public function save(ProductPrototype $productPrototype, array $options = []): void
    {
        $this->productPrototypeRepository->add($productPrototype);
        $this->entityManager->flush();
    }

    public function uploadFile(ProductPrototype $productPrototype, $transactionFile, $uploadDirectory): void
    {
        if ($transactionFile) {
            try {
                $filename = $productPrototype->getId() . '.' . $productPrototype->getProductionFileExtension();
                $transactionFile->move($uploadDirectory, $filename);
            } catch (FileException $e) {
            }
        }
    }
}
