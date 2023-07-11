<?php

namespace App\Sync\Stock;

use App\Common\Sync\EntitySyncScan;

class AdjustmentStockHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'adjustmentStockMaterialDetails' => null,
            'adjustmentStockPaperDetails' => null,
            'adjustmentStockProductDetails' => null,
        ]);
    }
}
