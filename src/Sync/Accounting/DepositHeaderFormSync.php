<?php

namespace App\Sync\Accounting;

use App\Common\Sync\EntitySyncScan;

class DepositHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'depositDetails' => null,
        ]);
    }
}
