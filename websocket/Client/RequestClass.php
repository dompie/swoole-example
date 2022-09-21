<?php
/*
 * Author: Dominik Piekarski <code@dompie.de>
 * Created at: 2022/09/01 15:01
 */
declare(strict_types=1);

namespace Websocket\Client;

abstract class RequestClass implements RequestClassInterface
{
    protected Request $request;

    public function getRequest(): Request
    {
        return $this->request;
    }
}
