<?php
/*
 * Author: Dominik Piekarski <code@dompie.de>
 * Created at: 2022/09/05 21:03
 */
declare(strict_types=1);

namespace Websocket\Client;

use Co\Http\Client;
use Websocket\SocketLogger;

abstract class HttpClient
{
    /**
     * @var string[]
     */
    protected static array $host = [];
    /**
     * @var int[]
     */
    protected static array $port = [];
    /**
     * @var bool[]
     */
    protected static array $useSsl = [];

    public static function setBaseUri(string $baseUri): void
    {
        self::$host[static::class] = substr($baseUri, strrpos($baseUri, '/') + 1);
        self::$port[static::class] = str_starts_with($baseUri, 'https') ? 443 : 80;
        self::$useSsl[static::class] = str_starts_with($baseUri, 'https');
    }

    public static function blockingClient(): Client
    {
        $client = new Client(self::$host[static::class], self::$port[static::class], self::$useSsl[static::class]);
        self::initialize($client);

        return $client;
    }

    public static function nonBlockingClient(): Client
    {
        $client = new Client(self::$host[static::class], self::$port[static::class], self::$useSsl[static::class]);
        self::initialize($client, true);

        return $client;
    }

    protected static function initialize(Client $client, $defer = false)
    {
        $client->set([
            'timeout' => 5,
            'defer' => $defer,//when true, requests will not block and run in background and status code will be empty
            'keep_alive' => true
        ]);
    }

    /**
     * @param int $fd
     * @param RequestClassInterface $requestClass
     * @param Client|null $client
     * @return mixed
     */
    public static function executeBlockingRequest(int $fd, RequestClassInterface $requestClass, Client $client = null)
    {
        $client = $client ?? self::blockingClient();

        $start = microtime(true);
        if (!self::executeRequest($client, $requestClass)) {
            SocketLogger::error("conn#$fd: {$requestClass->getRequest()->getRequestTarget()} executeRequest() returned false");
        }
        SocketLogger::debug(sprintf('%s %s%s in %05fs', $requestClass->getRequest()->getMethod(), self::$host[static::class], $requestClass->getRequest()->getRequestTarget(), (microtime(true) - $start)));
        $statusCode = (string)$client->getStatusCode();
        if ($statusCode[0] !== '2') {
            SocketLogger::error("conn#$fd: {$requestClass->getRequest()->getRequestTarget()} response $statusCode: " . var_export($client->getBody(), true));
            return null;
        }

        return $requestClass->processResponseData($client->getStatusCode(), $client->getBody(), $client->getHeaders());
    }

    /**
     * request() is always blocking. It does not need a $connectionFd from websocket.
     * @param RequestClassInterface $requestClass
     * @return null
     */
    public static function doRequest(RequestClassInterface $requestClass)
    {
        $client = self::blockingClient();
        if (self::executeRequest($client, $requestClass)) {
            return $requestClass->processResponseData($client->getStatusCode(), $client->getBody(), $client->getHeaders());
        }
        return null;
    }

    public static function executeNonBlockingRequest(RequestClassInterface $request, Client $client = null): void
    {
        $client = $client ?? self::nonBlockingClient();
        self::executeRequest($client, $request);
    }

    protected static function executeRequest(Client $client, RequestClassInterface $requestClass): bool
    {
        $requestHeaders = $requestClass->getRequest()->getHeaders();
        foreach ($requestHeaders as $key => $values) {
            $requestHeaders[$key] = (is_array($values) ? implode(',', $values) : $values);
        }

        $client->setHeaders($requestHeaders);
        $client->setMethod($requestClass->getRequest()->getMethod());
        $client->setData($requestClass->getRequest()->getStringBody());
        $request = $requestClass->getRequest()->withHeader('Host', self::$host[static::class]);
        return $client->execute($request->getRequestTarget());
    }
}
