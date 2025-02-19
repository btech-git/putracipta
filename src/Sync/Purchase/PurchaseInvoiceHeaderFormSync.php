<?php

namespace App\Sync\Purchase;

use App\Common\Sync\EntitySyncScan;
use App\Entity\Purchase\PurchaseInvoiceDetail;
use App\Entity\Purchase\PurchaseInvoiceHeader;

class PurchaseInvoiceHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupAssociations(PurchaseInvoiceHeader::class);
        $this->setupAssociations(PurchaseInvoiceDetail::class);
    }
}
