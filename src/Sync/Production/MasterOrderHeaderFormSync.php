<?php

namespace App\Sync\Production;

use App\Common\Sync\EntitySyncScan;

class MasterOrderHeaderFormSync
{
    use EntitySyncScan;

    public function __construct()
    {
        $this->setupRelations([
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
        ]);
    }
}
