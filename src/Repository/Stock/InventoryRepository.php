<?php

namespace App\Repository\Stock;

use App\Common\Doctrine\Repository\EntityAdd;
use App\Common\Doctrine\Repository\EntityDataFetch;
use App\Common\Doctrine\Repository\EntityRemove;
use App\Entity\Master\Warehouse;
use App\Entity\Stock\Inventory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class InventoryRepository extends ServiceEntityRepository
{
    use EntityDataFetch, EntityAdd, EntityRemove;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inventory::class);
    }

    public function getMaterialStockQuantityList(Warehouse $warehouse, array $materials): array
    {
        $dql = 'SELECT IDENTITY(e.material) AS materialId, SUM(e.quantityIn - e.quantityOut) AS stockQuantity
                FROM ' . Inventory::class . ' e
                WHERE e.warehouse = :warehouse AND e.material IN (:materials) AND e.isReversed = false
                GROUP BY e.warehouse, e.material';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('warehouse', $warehouse);
        $query->setParameter('materials', $materials);
        $stockQuantityList = $query->getScalarResult();

        return $stockQuantityList;
    }

    public function getPaperStockQuantityList(Warehouse $warehouse, array $papers): array
    {
        $dql = 'SELECT IDENTITY(e.paper) AS paperId, SUM(e.quantityIn - e.quantityOut) AS stockQuantity
                FROM ' . Inventory::class . ' e
                WHERE e.warehouse = :warehouse AND e.paper IN (:papers) AND e.isReversed = false
                GROUP BY e.warehouse, e.paper';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('warehouse', $warehouse);
        $query->setParameter('papers', $papers);
        $stockQuantityList = $query->getScalarResult();

        return $stockQuantityList;
    }

    public function getProductStockQuantityList(Warehouse $warehouse, array $products): array
    {
        $dql = 'SELECT IDENTITY(e.product) AS productId, SUM(e.quantityIn - e.quantityOut) AS stockQuantity
                FROM ' . Inventory::class . ' e
                WHERE e.warehouse = :warehouse AND e.product IN (:products) AND e.isReversed = false
                GROUP BY e.warehouse, e.product';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('warehouse', $warehouse);
        $query->setParameter('products', $products);
        $stockQuantityList = $query->getScalarResult();

        return $stockQuantityList;
    }

    public function getAllWarehousePaperStockQuantityList($paper)
    {
        $dql = 'SELECT IDENTITY(e.paper) AS paperId, SUM(e.quantityIn - e.quantityOut) AS stockQuantity
                FROM ' . Inventory::class . ' e
                WHERE e.paper IN (:paper) AND e.isReversed = false
                GROUP BY e.paper';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('paper', $paper);
        $stockQuantityList = $query->getScalarResult();

        return $stockQuantityList;
    }

    public function getAllWarehouseProductStockQuantityList(array $products): array
    {
        $dql = 'SELECT IDENTITY(e.product) AS productId, SUM(e.quantityIn - e.quantityOut) AS stockQuantity
                FROM ' . Inventory::class . ' e
                WHERE e.product IN (:products) AND e.isReversed = false
                GROUP BY e.product';

        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('products', $products);
        $stockQuantityList = $query->getScalarResult();

        return $stockQuantityList;
    }
}
