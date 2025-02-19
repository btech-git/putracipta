<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Purchase\ReceiveDetail;
use App\Entity\Purchase\ReceiveHeader;

class ReceiveHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(ReceiveHeader::class);
        $this->setupAssociations(ReceiveDetail::class);
    }
}
