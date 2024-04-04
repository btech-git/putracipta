<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DielineMillar;
use App\Entity\Support\Idempotent;
use App\Repository\Master\DielineMillarRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DielineMillarFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private DielineMillarRepository $dielineMillarRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->dielineMillarRepository = $entityManager->getRepository(DielineMillar::class);
    }

    public function finalize(DielineMillar $dielineMillar, array $options = []): void
    {
        $product = $dielineMillar->getProduct();
        if (!empty($product)) {
            $dielineMillar->setName($product->getName());
        }
    }

    public function save(DielineMillar $dielineMillar, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->dielineMillarRepository->add($dielineMillar);
        $this->entityManager->flush();
    }
}
