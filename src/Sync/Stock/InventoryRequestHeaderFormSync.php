<?php

namespace App\Sync\Stock;

use App\Common\Sync\EntitySyncScan;

class InventoryRequestHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'inventoryRequestPaperDetails' => [
                'inventoryReleasePaperDetails' => null,
            ],
            'inventoryRequestMaterialDetails' => [
                'inventoryReleaseMaterialDetails' => null,
            ],
            'inventoryReleaseHeaders' => [
                'inventoryReleaseMaterialDetails' => null,
                'inventoryReleasePaperDetails' => null,
            ],
        ]);
    }
}
