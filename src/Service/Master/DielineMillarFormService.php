<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DielineMillar;
use App\Entity\Master\DielineMillarDetail;
use App\Entity\Support\Idempotent;
use App\Repository\Master\DielineMillarRepository;
use App\Repository\Master\DielineMillarDetailRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DielineMillarFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private DielineMillarRepository $dielineMillarRepository;
    private DielineMillarDetailRepository $dielineMillarDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->dielineMillarRepository = $entityManager->getRepository(DielineMillar::class);
        $this->dielineMillarDetailRepository = $entityManager->getRepository(DielineMillarDetail::class);
    }

    public function initialize(DielineMillar $dielineMillar, array $options = []): void
    {
        list($datetime) = [$options['datetime']];

        if (empty($dielineMillar->getId())) {
            $dielineMillar->setDate($datetime);
        }
    }

    public function finalize(DielineMillar $dielineMillar, array $options = []): void
    {
        $productCodeList = array();
        $productNameList = array();
        foreach ($dielineMillar->getDielineMillarDetails() as $dielineMillarDetail) {
            $product = $dielineMillarDetail->getProduct();
            $productCodeList[] = $product->getCode();
            $productNameList[] = $product->getName();
        }
        $dielineMillar->setCode(implode(', ', $productCodeList));
        $dielineMillar->setName(implode(', ', $productNameList));
    }

    public function save(DielineMillar $dielineMillar, array $options = []): void
    {
//        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
//        $this->idempotentRepository->add($idempotent);
        $this->dielineMillarRepository->add($dielineMillar);
        foreach ($dielineMillar->getDielineMillarDetails() as $dielineMillarDetail) {
            $this->dielineMillarDetailRepository->add($dielineMillarDetail);
        }
        $this->entityManager->flush();
    }
}
