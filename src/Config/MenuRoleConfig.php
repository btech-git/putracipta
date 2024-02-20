<?php

namespace App\Config;

class MenuRoleConfig
{
    public static function getConfig(): array
    {
        return [
            'MENU_MASTER' => ['MENU_EMPLOYEE', 'MENU_SUPPLIER', 'MENU_CUSTOMER'],
            'MENU_EMPLOYEE' => ['ROLE_EMPLOYEE_ADD', 'ROLE_EMPLOYEE_EDIT'],
            'MENU_SUPPLIER' => ['ROLE_SUPPLIER_ADD', 'ROLE_SUPPLIER_EDIT'],
            'MENU_CUSTOMER' => ['ROLE_CUSTOMER_ADD', 'ROLE_CUSTOMER_EDIT'],
        ];
    }
}
