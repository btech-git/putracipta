<?php

namespace App\Sync\Transaction;

use App\Common\Sync\EntitySyncScan;

class SaleReturnHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'saleReturnDetails' => null,
        ]);
    }
}
