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
    private DesignCodeRepository $designCodeRepository;
    private DiecutKnifeRepository $diecutKnifeRepository;
    private DielineMillarRepository $dielineMillarRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->productRepository = $entityManager->getRepository(Product::class);
        $this->designCodeRepository = $entityManager->getRepository(DesignCode::class);
        $this->diecutKnifeRepository = $entityManager->getRepository(DiecutKnife::class);
        $this->dielineMillarRepository = $entityManager->getRepository(DielineMillar::class);
    }

    public function finalize(Product $product, array $options = []): void
    {
        if ($options['transactionFile']) {
            $product->setFileExtension($options['transactionFile']->guessExtension());
        }
        
        foreach ($product->getDesignCodes() as $designCode) {
            $designCode->setCustomer($product->getCustomer());
        }
        
        foreach ($product->getDiecutKnives() as $diecutKnife) {
            $diecutKnife->setCustomer($product->getCustomer());
        }
        
        foreach ($product->getDielineMillars() as $dielineMillar) {
            $dielineMillar->setCustomer($product->getCustomer());
        }
    }

    public function save(Product $product, array $options = []): void
    {
        $this->productRepository->add($product);
        foreach ($product->getDesignCodes() as $designCode) {
            $this->designCodeRepository->add($designCode);
        }
        foreach ($product->getDiecutKnives() as $diecutKnife) {
            $this->diecutKnifeRepository->add($diecutKnife);
        }
        foreach ($product->getDielineMillars() as $dielineMillar) {
            $this->dielineMillarRepository->add($dielineMillar);
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
