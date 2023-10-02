<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Paper;
use App\Entity\Support\Idempotent;
use App\Repository\Master\PaperRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PaperFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private PaperRepository $paperRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->paperRepository = $entityManager->getRepository(Paper::class);
    }

    public function save(Paper $paper, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->paperRepository->add($paper);
        $this->entityManager->flush();
    }
}
