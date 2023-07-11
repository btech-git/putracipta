<?php

namespace App\Sync\Production;

use App\Common\Sync\EntitySyncScan;

class ProductPrototypeFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'productDevelopments' => null,
        ]);
    }
}
