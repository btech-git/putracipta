<?php

namespace App\Common\Idempotent;

use Symfony\Component\HttpFoundation\RequestStack;

class IdempotentManager
{
    public const TOKEN_NAME = '_idempotent_token';
    public const MIN_TOKEN_VALUE = 1;
    public const MAX_TOKEN_VALUE = 2000000000;

    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function add()
    {
        $request = $this->requestStack->getCurrentRequest();
        if ($request->request->has(self::TOKEN_NAME) && $request->attributes->has('_controller')) {
            $token = $request->request->get(self::TOKEN_NAME);
            $controllerName = $request->attributes->get('_controller');
        } else {
            throw new \Exception('An idempotent token has not been set');
        }
    }
}
