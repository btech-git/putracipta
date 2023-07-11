<?php

namespace App\Sync\Transaction;

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
