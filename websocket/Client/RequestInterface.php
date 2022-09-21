<?php
/*
 * Author: Dominik Piekarski <code@dompie.de>
 * Created at: 2022/09/01 12:59
 */
declare(strict_types=1);

namespace Websocket\Client;

interface RequestInterface
{
    public function withApiToken(string $token): self;

    public function getApiToken(): string;

    public function withUriPath(string $path): self;

    public function getUriPath(): string;

    public function withStringBody(string $body): self;

    public function getStringBody(): string;

    public function withFrontendHeader(): self;
}
