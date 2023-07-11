<?php

namespace App\Sync\Accounting;

use App\Common\Sync\EntitySyncScan;

class ExpenseHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'expenseDetails' => null,
        ]);
    }
}
