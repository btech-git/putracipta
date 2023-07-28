<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;

class PurchaseInvoiceHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'purchaseInvoiceDetails' => null,
            'purchasePaymentDetails' => null,
        ]);
    }
}
