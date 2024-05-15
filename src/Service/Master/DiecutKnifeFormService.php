<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DiecutKnife;
use App\Entity\Master\DiecutKnifeDetail;
use App\Entity\Support\Idempotent;
use App\Repository\Master\DiecutKnifeRepository;
use App\Repository\Master\DiecutKnifeDetailRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DiecutKnifeFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private DiecutKnifeRepository $diecutKnifeRepository;
    private DiecutKnifeDetailRepository $diecutKnifeDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->diecutKnifeRepository = $entityManager->getRepository(DiecutKnife::class);
        $this->diecutKnifeDetailRepository = $entityManager->getRepository(DiecutKnifeDetail::class);
    }

    public function initialize(DiecutKnife $diecutKnife, array $options = []): void
    {
        list($datetime, $user) = [$options['datetime'], $options['user']];

        if (empty($diecutKnife->getId())) {
            $diecutKnife->setDate($datetime);
        }
    }

    public function finalize(DiecutKnife $diecutKnife, array $options = []): void
    {
        $productCodeList = array();
        $productNameList = array();
        foreach ($diecutKnife->getDiecutKnifeDetails() as $diecutKnifeDetail) {
            $product = $diecutKnifeDetail->getProduct();
            $productCodeList[] = $product->getCode();
            $productNameList[] = $product->getName();
        }
        $diecutKnife->setCode(implode(', ', $productCodeList));
        $diecutKnife->setName(implode(', ', $productNameList));
    }

    public function save(DiecutKnife $diecutKnife, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->diecutKnifeRepository->add($diecutKnife);
        foreach ($diecutKnife->getDiecutKnifeDetails() as $diecutKnifeDetail) {
            $this->diecutKnifeDetailRepository->add($diecutKnifeDetail);
        }
        $this->entityManager->flush();
    }
}
