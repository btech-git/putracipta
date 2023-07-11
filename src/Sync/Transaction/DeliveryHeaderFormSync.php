<?php

namespace App\Sync\Transaction;

use App\Common\Sync\EntitySyncScan;

class DeliveryHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'deliveryDetails' => [
                'saleReturnDetails' => null,
                'saleInvoiceDetails' => null,
            ],
            'saleReturnHeaders' => [
                'saleReturnDetails' => null,
            ],
        ]);
    }
}
