<?php

namespace App\Service\Master;

use App\Common\Idempotent\IdempotentUtility;
use App\Entity\Master\Material;
use App\Entity\Support\Idempotent;
use App\Repository\Master\MaterialRepository;
use App\Repository\Support\IdempotentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class MaterialFormService
{
    private RequestStack $requestStack;
    private EntityManagerInterface $entityManager;
    private IdempotentRepository $idempotentRepository;
    private MaterialRepository $materialRepository;

    public function __construct(RequestStack $requestStack, EntityManagerInterface $entityManager)
    {
        $this->requestStack = $requestStack;
        $this->entityManager = $entityManager;
        $this->idempotentRepository = $entityManager->getRepository(Idempotent::class);
        $this->materialRepository = $entityManager->getRepository(Material::class);
    }

    public function finalize(Material $material, array $options = []): void
    {
//        if ($material->getId() === null) {
            $materialSubCategory = $material->getMaterialSubCategory();
            $lastMaterial = $this->materialRepository->findRecentBy($materialSubCategory);
            $currentMaterial = ($lastMaterial === null) ? $material : $lastMaterial;
            $material->setCodeOrdinalToNext($currentMaterial->getCodeOrdinal());
//        }
    }
    
    public function save(Material $material, array $options = []): void
    {
        $idempotent = IdempotentUtility::create(Idempotent::class, $this->requestStack->getCurrentRequest());
        $this->idempotentRepository->add($idempotent);
        $this->materialRepository->add($material);
        $this->entityManager->flush();
    }
}
