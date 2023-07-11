<?php

namespace App\Sync\Transaction;

use App\Common\Sync\EntitySyncScan;

class SaleInvoiceHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'salePaymentDetails' => null,
            'saleInvoiceDetails' => null,
        ]);
    }
}
