<?php
/*
 * Author: Dominik Piekarski <code@dompie.de>
 * Created at: 2022/09/01 12:42
 */
declare(strict_types=1);

namespace Websocket\Client;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\StreamInterface;

class Request extends \GuzzleHttp\Psr7\Request implements RequestInterface
{
    protected string $body = '';
    protected string $apiToken = '';

    public function __construct(string $method, $uri, array $headers = [], $body = null, string $version = '1.1')
    {
        parent::__construct($method, $uri, $this->applyDefaultHeaders($headers), $body, $version);
    }

    private function applyDefaultHeaders(array $headers): array
    {
        return array_merge([
            'Accept-Encoding' => 'gzip',
        ], $headers);
    }

    /**
     * @param string $body
     * @return Request
     */
    public function withStringBody(string $body): self
    {
        $this->body = $body;
        return $this;
    }

    public function getStringBody(): string
    {
        return $this->body;
    }

    public function getUriPath(): string
    {
        return $this->getUri()->getPath();
    }

    public function withBody(StreamInterface $body): MessageInterface
    {
        throw new \Exception('Use withStringBody()');
    }

    public function getBody(): StreamInterface
    {
        throw new \Exception('Use getStringBody()');
    }

    /**
     * @param string $path
     * @return Request
     */
    public function withUriPath(string $path): self
    {
        return $this->withUri(new Uri($path));
    }

    /**
     * @param string $token
     * @return Request
     */
    public function withApiToken(string $token): self
    {
        return $this->withHeader('Authorization', "Bearer $token");
    }

    public function getApiToken(): string
    {
        return $this->apiToken;
    }

    public function withFrontendHeader(): self
    {
        return $this;
    }
}
