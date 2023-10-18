<?php

namespace App\Service\Production;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\ProductPrototype;
use App\Entity\Support\Idempotent;
use App\Repository\Production\ProductPrototypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductPrototypeFormService
{
    private EntityManagerInterface $entityManager;
    private ProductPrototypeRepository $productPrototypeRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->productPrototypeRepository = $entityManager->getRepository(ProductPrototype::class);
    }

    public function initialize(ProductPrototype $productPrototype, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($productPrototype->getId())) {
            $productPrototype->setCreatedTransactionDateTime($datetime);
            $productPrototype->setCreatedTransactionUser($user);
        } else {
            $productPrototype->setModifiedTransactionDateTime($datetime);
            $productPrototype->setModifiedTransactionUser($user);
        }
    }

    public function finalize(ProductPrototype $productPrototype, array $options = []): void
    {
        if ($productPrototype->getTransactionDate() !== null && $productPrototype->getId() === null) {
            $year = $productPrototype->getTransactionDate()->format('y');
            $month = $productPrototype->getTransactionDate()->format('m');
            $lastProductPrototype = $this->productPrototypeRepository->findRecentBy($year, $month);
            $currentProductPrototype = ($lastProductPrototype === null) ? $productPrototype : $lastProductPrototype;
            $productPrototype->setCodeNumberToNext($currentProductPrototype->getCodeNumber(), $year, $month);
        }
        
//        if ($options['transactionFile']) {
//            $productPrototype->setProductionFileExtension($options['transactionFile']->guessExtension());
//        }
    }

    public function save(ProductPrototype $productPrototype, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->productPrototypeRepository->add($productPrototype);
        $this->entityManager->flush();
    }

//    public function uploadFile(ProductPrototype $productPrototype, $transactionFile, $uploadDirectory): void
//    {
//        if ($transactionFile) {
//            try {
//                $filename = $productPrototype->getId() . '.' . $productPrototype->getProductionFileExtension();
//                $transactionFile->move($uploadDirectory, $filename);
//            } catch (FileException $e) {
//            }
//        }
//    }
}
