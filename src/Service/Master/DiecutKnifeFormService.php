<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DiecutKnife;
use App\Entity\Support\Idempotent;
use App\Repository\Master\DiecutKnifeRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DiecutKnifeFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private DiecutKnifeRepository $diecutKnifeRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->diecutKnifeRepository = $entityManager->getRepository(DiecutKnife::class);
    }

    public function save(DiecutKnife $diecutKnife, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->diecutKnifeRepository->add($diecutKnife);
        $this->entityManager->flush();
    }
}
