<?php

namespace App\Sync\Transaction;

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
