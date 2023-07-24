<?php

namespace App\Service\Master;

use App\Entity\Master\DesignCode;
use App\Entity\Master\DiecutKnife;
use App\Entity\Master\DielineMillar;
use App\Entity\Master\Product;
use App\Repository\Master\DesignCodeRepository;
use App\Repository\Master\DiecutKnifeRepository;
use App\Repository\Master\DielineMillarRepository;
use App\Repository\Master\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

class ProductFormService
{
    private EntityManagerInterface $entityManager;
    private ProductRepository $productRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $entityManager->getRepository(Product::class);
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
