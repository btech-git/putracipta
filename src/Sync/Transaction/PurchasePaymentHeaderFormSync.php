<?php

namespace App\Sync\Transaction;

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
