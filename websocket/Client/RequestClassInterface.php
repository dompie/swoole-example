<?php
/*
 * Author: Dominik Piekarski <code@dompie.de>
 * Created at: 2022/09/01 13:37
 */
declare(strict_types=1);

namespace Websocket\Client;


interface RequestClassInterface
{
    public function getRequest(): Request;

    public function processResponseData(int $statusCode, string $responseBody, array $responseHeaders);
// This could be a good entry point to further optimizations.
//    public function isCacheable(): bool;
//    public function getCacheKey(): string;
}
