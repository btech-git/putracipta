<?php

namespace App\Support\Production;

use App\Entity\Production\MasterOrderHeader;
use App\Support\SupportEntityBuilder;

trait MasterOrderHeaderFormSupport
{
    use SupportEntityBuilder;

    private function transactionLogNewData(MasterOrderHeader $masterOrderHeader): array
    {
        $account = $masterOrderHeader->getAccount();
        return [
            'codeNumber' => $masterOrderHeader->getCodeNumber(),
            'transactionDate' => $masterOrderHeader->getTransactionDate(),
            'note' => $masterOrderHeader->getNote(),
            'account' => [
                'name' => $account->getName(),
                'code' => $account->getCode(),
            ],
            'totalAmount' => $masterOrderHeader->getTotalAmount(),
            'masterOrderDetails' => array_map(function($masterOrderDetail) {
                return [
                    'account' => [
                        'code' => $masterOrderDetail->getAccount()->getCode(),
                        'name' => $masterOrderDetail->getAccount()->getName(),
                    ],
                    'description' => $masterOrderDetail->getDescription(),
                    'amount' => $masterOrderDetail->getAmount(),
                    'memo' => $masterOrderDetail->getMemo(),
                ];
            }, $masterOrderHeader->getMasterOrderDetails()->toArray()),
        ];
    }
}
