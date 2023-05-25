<?php

namespace App\Common\Sync;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

trait EntitySyncScan
{
    private array $relations = [];
    private array $managedEntities = [];
    private array $persistedEntities = [];
    private bool $oldEntitiesScanned = false;
    private bool $newEntitiesScanned = false;

    public function scanForOldEntities($rootEntity): void
    {
        if (!$this->oldEntitiesScanned && !$this->newEntitiesScanned) {
            $this->putEntityToScanned($rootEntity, '_', false);
            $this->scanForEntities($rootEntity, $this->relations, false);
            $this->oldEntitiesScanned = true;
        }
    }

    public function scanForNewEntities($rootEntity): void
    {
        if ($this->oldEntitiesScanned && !$this->newEntitiesScanned) {
            $this->putEntityToScanned($rootEntity, '_', true);
            $this->scanForEntities($rootEntity, $this->relations, true);
            $this->newEntitiesScanned = true;
        }
    }

    public function update(EntityManagerInterface $entityManager): void
    {
        foreach ($this->persistedEntities as $persistedEntity) {
            $entityManager->persist($persistedEntity);
        }
        foreach ($this->managedEntities as $managedEntityItem) {
            if ($managedEntityItem[1]) {
                $entityManager->persist($managedEntityItem[0]);
            } else {
                $entityManager->remove($managedEntityItem[0]);
            }
        }
    }

    private function setupRelations(array $relations): void
    {
        $this->relations = $relations;
    }

    private function scanForEntities($parentEntity, array|null $relations, bool $isNew): void
    {
        if ($relations !== null) {
            foreach ($relations as $relationName => $subRelations) {
                $getterName = 'get' . ucfirst($relationName);
                $objectOrCollection = $parentEntity->$getterName();
                $entities = $objectOrCollection instanceof Collection ? $objectOrCollection->getValues() : [$objectOrCollection];
                foreach ($entities as $entity) {
                    $this->putEntityToScanned($entity, $relationName, $isNew);
                    if (is_array($subRelations)) {
                        $this->scanForEntities($entity, $subRelations, $isNew);
                    }
                }
            }
        }
    }

    private function putEntityToScanned($entity, string $relationName, bool $isNew): void
    {
        $entityId = $entity->getId();
        if ($entityId === null) {
            if ($isNew) {
                $this->persistedEntities[] = $entity;
            }
        } else {
            $key = $relationName . '|' . $entityId;
            $this->managedEntities[$key] = [$entity, isset($this->managedEntities[$key])];
        }
    }
}
