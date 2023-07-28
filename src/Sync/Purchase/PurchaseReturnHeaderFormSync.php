<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;

class PurchaseReturnHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'purchaseReturnDetails' => null,
        ]);
    }
}
