<?php

namespace App\Sync\Transaction;

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
