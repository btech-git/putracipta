<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;

class PurchaseRequestHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'purchaseRequestDetails' => [
                'purchaseOrderDetails' => [
                    'receiveDetails' => [
                        'purchaseReturnDetails' => null,
                        'purchaseInvoiceDetails' => null,
                    ],
                ],
            ],

        ]);
    }
}
