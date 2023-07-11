<?php

namespace App\Sync\Transaction;

use App\Common\Sync\EntitySyncScan;

class SaleOrderHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'saleOrderDetails' => [
                'deliveryDetails' => [
                    'saleReturnDetails' => null,
                    'saleInvoiceDetails' => null,
                ],
                'masterOrderProductDetails' => null,
            ],
        ]);
    }
}
