<?php

namespace App\Sync\Accounting;

use App\Common\Sync\EntitySyncScan;

class JournalVoucherHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'journalVoucherDetails' => null,
        ]);
    }
}
