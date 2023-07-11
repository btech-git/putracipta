<?php

namespace App\Sync\Stock;

use App\Common\Sync\EntitySyncScan;

class InventoryReleaseHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'inventoryReleaseMaterialDetails' => null,
            'inventoryReleasePaperDetails' => null,
        ]);
    }
}
