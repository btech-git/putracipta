<?php

namespace App\Sync\Sale;

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
