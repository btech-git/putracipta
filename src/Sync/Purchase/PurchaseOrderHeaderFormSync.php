<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;

class PurchaseOrderHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'purchaseOrderDetails' => [
                'receiveDetails' => [
                    'purchaseReturnDetails' => null,
                    'purchaseInvoiceDetails' => null,
                ],
            ],
            'receiveHeaders' => [
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
            ],
        ]);
    }
}
