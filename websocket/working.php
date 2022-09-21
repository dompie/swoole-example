<?php
/*
 * Author: Dominik Piekarski <code@dompie.de>
 * Created at: 2022/08/28 20:59
 */
declare(strict_types=1);

error_reporting(E_ALL);

use Swoole\Runtime;

Runtime::enableCoroutine(true);

if (empty($_SERVER['argv'][1])) {
    echo 'API client host required!' . PHP_EOL;
    return;
}

$apiToken = 'abc123';
$apiHost = $_SERVER['argv'][1];
go(function() use ($apiToken, $apiHost) {
    $client = new Co\Http\Client($apiHost, 443, true);
    $client->setHeaders(['Authorization' => "Bearer $apiToken"]);
    if (!$client->execute('/api/me')) {
        echo "executeRequest() somehow failed".PHP_EOL;
    }
    echo 'example1: '.PHP_EOL;
    //$client->getStatusCode();
    var_dump($client->getBody());
});
