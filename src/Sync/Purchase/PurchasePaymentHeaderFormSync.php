<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;

class PurchasePaymentHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'purchasePaymentDetails' => null,
        ]);
    }
}
