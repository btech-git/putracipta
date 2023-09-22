<?php

namespace App\Service\Master;

use App\Entity\Master\DesignCode;
use App\Entity\Master\DesignCodeProcessDetail;
use App\Repository\Master\DesignCodeRepository;
use App\Repository\Master\DesignCodeProcessDetailRepository;
use Doctrine\ORM\EntityManagerInterface;

class DesignCodeFormService
{
    private EntityManagerInterface $entityManager;
    private DesignCodeRepository $designCodeRepository;
    private DesignCodeProcessDetailRepository $designCodeProcessDetailRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->designCodeRepository = $entityManager->getRepository(DesignCode::class);
        $this->designCodeProcessDetailRepository = $entityManager->getRepository(DesignCodeProcessDetail::class);
    }

    public function save(DesignCode $designCode, array $options = []): void
    {
        $this->designCodeRepository->add($designCode);
        foreach ($designCode->getDesignCodeProcessDetails() as $designCodeProcessDetail) {
            $this->designCodeProcessDetailRepository->add($designCodeProcessDetail);
        }
        $this->entityManager->flush();
    }
}
