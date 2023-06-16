<?php

namespace App\Service\Master;

use App\Entity\Master\DesignCode;
use App\Entity\Master\Product;
use App\Repository\Master\DesignCodeRepository;
use App\Repository\Master\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductFormService
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;
    private DesignCodeRepository $designCodeRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $entityManager->getRepository(Product::class);
        $this->designCodeRepository = $entityManager->getRepository(DesignCode::class);
    }

    public function finalize(Product $product, array $options = []): void
    {
        if ($options['transactionFile']) {
            $product->setFileExtension($options['transactionFile']->guessExtension());
        }
    }

    public function save(Product $product, array $options = []): void
    {
        $this->productRepository->add($product);
        foreach ($product->getDesignCodes() as $designCode) {
            $this->designCodeRepository->add($designCode);
        }
        $this->entityManager->flush();
    }

    public function uploadFile(Product $product, $transactionFile, $uploadDirectory): void
    {
        if ($transactionFile) {
            try {
                $filename = $product->getId() . '.' . $product->getFileExtension();
                $transactionFile->move($uploadDirectory, $filename);
            } catch (FileException $e) {
            }
        }
    }
}
