<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Purchase\PurchaseOrderDetail;
use App\Entity\Purchase\PurchaseOrderHeader;

class PurchaseOrderHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(PurchaseOrderHeader::class);
        $this->setupAssociations(PurchaseOrderDetail::class);
    }
}
