<?php

namespace App\Util;

use App\Config\MenuRoleConfig;
use Symfony\Component\Security\Core\Security;

class MenuRole
{
    private Security $security;
    private array $config;

    public function __construct(Security $security)
    {
        $this->security = $security;
        $this->config = MenuRoleConfig::getConfig();
    }

    public function isAuthorized(string $name): bool
    {
        if (isset($this->config[$name])) {
            $valid = false;
            foreach ($this->config[$name] as $itemName) {
                $valid = $valid || $this->isAuthorized($itemName);
                if ($valid) {
                    break;
                }
            }
            return $valid;
        } else {
            return $this->security->isGranted($name);
        }
    }
}
