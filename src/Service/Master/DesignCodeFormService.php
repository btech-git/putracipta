<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\DesignCode;
use App\Entity\Master\DesignCodeProcessDetail;
use App\Entity\Support\Idempotent;
use App\Repository\Master\DesignCodeRepository;
use App\Repository\Master\DesignCodeProcessDetailRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class DesignCodeFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private DesignCodeRepository $designCodeRepository;
    private DesignCodeProcessDetailRepository $designCodeProcessDetailRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->designCodeRepository = $entityManager->getRepository(DesignCode::class);
        $this->designCodeProcessDetailRepository = $entityManager->getRepository(DesignCodeProcessDetail::class);
    }

    public function save(DesignCode $designCode, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->designCodeRepository->add($designCode);
        foreach ($designCode->getDesignCodeProcessDetails() as $designCodeProcessDetail) {
            $this->designCodeProcessDetailRepository->add($designCodeProcessDetail);
        }
        $this->entityManager->flush();
    }
}
