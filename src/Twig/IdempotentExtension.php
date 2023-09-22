<?php

namespace App\Twig;

use App\Common\Idempotent\IdempotentManager;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class IdempotentExtension extends AbstractExtension
{
    public function getName(): string
    {
        return 'idempotent_extension';
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('idempotent_token_value', [$this, 'functionIdempotentTokenValue']),
            new TwigFunction('idempotent_token_name', [$this, 'functionIdempotentTokenName']),
        ];
    }

    public function functionIdempotentTokenValue(): int
    {
        return random_int(IdempotentManager::MIN_TOKEN_VALUE, IdempotentManager::MAX_TOKEN_VALUE);
    }

    public function functionIdempotentTokenName(): string
    {
        return IdempotentManager::TOKEN_NAME;
    }
}
