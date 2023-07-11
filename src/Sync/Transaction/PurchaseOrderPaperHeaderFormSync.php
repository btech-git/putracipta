<?php

namespace App\Sync\Transaction;

use App\Common\Sync\EntitySyncScan;

class PurchaseOrderPaperHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
            'purchaseOrderPaperDetails' => [
                'receiveDetails' => [
                    'purchaseReturnDetails' => null,
                    'purchaseInvoiceDetails' => null,
                ],
            ],
            'receiveHeaders' => [
                'receiveDetails' => [
                    'purchaseReturnDetails' => null,
                    'purchaseInvoiceDetails' => null,
                ],
                'purchaseInvoiceHeaders' => [
                    'purchaseInvoiceDetails' => null,
                    'purchasePaymentDetails' => null,
                ],
                'purchaseReturnHeaders' => [
                    'purchaseReturnDetails' => null,
                ],
            ],
            'masterOrderHeaders' => [
                'workOrderColorMixings' => null,
                'workOrderCuttingHeaders' => [
                    'workOrderCuttingMaterialDetails' => null,
                    'workOrderCuttingFinishedDetails' => null,
                ],
                'workOrderOffsetPrintingHeaders' => [
                    'workOrderOffsetPrintingDetails' => null,
                ],
                'workOrderPrepresses' => null,
                'workOrderVarnishHeaders' => [
                    'workOrderVarnishSettingDetails' => null,
                    'workOrderVarnishProductionDetails' => null,
                ],
                'workOrderVarnishSpotHeaders' => [
                    'workOrderVarnishSpotSettingDetails' => null,
                    'workOrderVarnishSpotProductionDetails' => null,
                ],
                'masterOrderDistributionDetails' => null,
                'masterOrderProductDetails' => null,
                'masterOrderProcessDetails' => null,
                'masterOrderCheckSheetDetails' => null,
            ],
        ]);
    }
}
