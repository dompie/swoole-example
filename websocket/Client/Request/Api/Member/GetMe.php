<?php
/*
 * Author: Dominik Piekarski <code@dompie.de>
 * Created at: 2022/08/26 09:38
 */
declare(strict_types=1);

namespace Websocket\Client\Request\Api\Member;

use Websocket\Client\Request;
use Websocket\Client\RequestClass;

class GetMe extends RequestClass
{
    public function __construct(string $apiToken)
    {
        $this->request = (new Request('GET', '/api/me'))
            ->withApiToken($apiToken);
    }

    public function processResponseData(int $statusCode, $responseBody, $responseHeaders): ?array
    {
        return json_decode($responseBody, true, 512, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRESERVE_ZERO_FRACTION);
    }
}
