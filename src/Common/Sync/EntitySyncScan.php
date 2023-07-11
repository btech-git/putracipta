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
        if ($this->oldEntitiesScanned && $this->newEntitiesScanned) {
            foreach ($this->persistedEntities as $persistedEntity) {
                $entityManager->persist($persistedEntity);
            }
            foreach ($this->managedEntities as $managedEntityItem) {
                if ($managedEntityItem[1] > $managedEntityItem[2]) {
                    $entityManager->remove($managedEntityItem[0]);
                }
            }
        }
    }

    public function getView(): array
    {
        $countByRelation = [];
        foreach (array_keys($this->managedEntities) as $key) {
            list($relationName, ) = explode('|', $key);
            if (!isset($countByRelation[$relationName])) {
                $countByRelation[$relationName] = 0;
            }
            $countByRelation[$relationName]++;
        }
        return [
            'countByRelation' => $countByRelation,
        ];
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
            if (!isset($this->managedEntities[$key])) {
                $this->managedEntities[$key] = [$entity, 0, 0];
            }
            if ($isNew) {
                $this->managedEntities[$key][2]++;
            } else {
                $this->managedEntities[$key][1]++;
            }
        }
    }
}
