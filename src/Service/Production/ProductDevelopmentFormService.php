<?php

namespace App\Service\Production;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\ProductDevelopment;
use App\Entity\Support\Idempotent;
use App\Repository\Production\ProductDevelopmentRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductDevelopmentFormService
{
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private ProductDevelopmentRepository $productDevelopmentRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
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
        if ($productDevelopment->getTransactionDate() !== null && $productDevelopment->getId() === null) {
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
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
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
