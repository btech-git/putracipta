<?php

namespace App\Sync\Purchase;

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
