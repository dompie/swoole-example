<?php
/*
 * Author: Dominik Piekarski <code@dompie.de>
 * Created at: 2022/08/28 20:59
 */
declare(strict_types=1);

error_reporting(E_ALL);

use Swoole\Runtime;

Runtime::enableCoroutine(true);

require_once __DIR__ . '/../vendor/autoload.php';

use Websocket\Client\ApiClient;
use Websocket\Client\Request\Api\Member\GetMe;

if (empty($_SERVER['argv'][1])) {
    echo 'API client host required!' . PHP_EOL;
    return;
}

ApiClient::setBaseUri($_SERVER['argv'][1]);

$apiToken = 'abc123';

go(function () use ($apiToken) {
    $user = ApiClient::executeBlockingRequest(2, new GetMe($apiToken));
    echo 'return value: ' . PHP_EOL;
    var_dump($user);

});
