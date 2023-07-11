<?php

namespace App\Sync\Stock;

use App\Common\Sync\EntitySyncScan;

class StockTransferHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'stockTransferMaterialDetails' => null,
            'stockTransferPaperDetails' => null,
            'stockTransferProductDetails' => null,
        ]);
    }
}
