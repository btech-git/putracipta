<?php

namespace App\Sync\Production;

use App\Common\Sync\EntitySyncScan;

class ProductDevelopmentFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            
        ]);
    }
}
