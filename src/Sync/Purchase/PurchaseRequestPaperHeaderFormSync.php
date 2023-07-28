<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;

class PurchaseRequestPaperHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'purchaseRequestPaperDetails' => [
                'purchaseOrderPaperDetails' => [
                    'receiveDetails' => [
                        'purchaseReturnDetails' => null,
                        'purchaseInvoiceDetails' => null,
                    ],
                ],
            ],
        ]);
    }
}
