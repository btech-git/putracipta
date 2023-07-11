<?php

namespace App\Sync\Transaction;

use App\Common\Sync\EntitySyncScan;

class ReceiveHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'receiveDetails' => [
                'purchaseReturnDetails' => null,
                'purchaseInvoiceDetails' => null,
            ],
            'purchaseInvoiceHeaders' => [
                'purchaseInvoiceDetails' => null,
                'purchasePaymentDetails' => null,
            ],
            'purchaseReturnHeaders' => [
                'purchaseReturnDetails' => null,
            ],
        ]);
    }
}
