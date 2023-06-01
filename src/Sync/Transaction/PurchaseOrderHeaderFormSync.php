<?php

namespace App\Sync\Transaction;

use App\Common\Sync\EntitySyncScan;
use Doctrine\ORM\EntityManagerInterface;

class PurchaseOrderHeaderFormSync
{
    use EntitySyncScan;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->setupRelations([
            'purchaseOrderDetails' => [
                'receiveDetails' => null,
            ],
        ]);
    }
}
