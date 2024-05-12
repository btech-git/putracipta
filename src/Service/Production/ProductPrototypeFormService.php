<?php

namespace App\Service\Production;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Production\ProductPrototype;
use App\Entity\Production\ProductPrototypeDetail;
use App\Entity\Support\Idempotent;
use App\Entity\Support\TransactionLog;
use App\Repository\Production\ProductPrototypeRepository;
use App\Repository\Production\ProductPrototypeDetailRepository;
use App\Repository\Support\IdempotentRepository;
use App\Repository\Support\TransactionLogRepository;
use App\Support\Production\ProductPrototypeFormSupport;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductPrototypeFormService
{
    use ProductPrototypeFormSupport;

    private EntityManagerInterface $entityManager;
    private TransactionLogRepository $transactionLogRepository;
    private IdempotentRepository $idempotentRepository;
    private ProductPrototypeRepository $productPrototypeRepository;
    private ProductPrototypeDetailRepository $productPrototypeDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->transactionLogRepository = $entityManager->getRepository(TransactionLog::class);
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->productPrototypeRepository = $entityManager->getRepository(ProductPrototype::class);
        $this->productPrototypeDetailRepository = $entityManager->getRepository(ProductPrototypeDetail::class);
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
    }

    public function save(ProductPrototype $productPrototype, array $options = []): void
    {
        $this->entityManager->wrapInTransaction(function($entityManager) use ($productPrototype) {
            $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
            $this->idempotentRepository->add($idempotent);
            $this->productPrototypeRepository->add($productPrototype);
            foreach ($productPrototype->getProductPrototypeDetails() as $productPrototypeDetail) {
                $this->productPrototypeDetailRepository->add($productPrototypeDetail);
            }
            $this->entityManager->flush();
            
            $transactionLog = $this->buildTransactionLog($productPrototype);
            $this->transactionLogRepository->add($transactionLog);
            $entityManager->flush();
        });
    }
}
