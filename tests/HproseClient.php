<?php
/**
 * Created by PhpStorm.
 * User: lidanyang
 * Date: 17/3/1
 * Time: 15:19
 */

require_once __DIR__ . '/../vendor/autoload.php';

use app\processor\TestRequest;

$client = new \Hprose\Socket\Client('tcp://127.0.0.1:9502', false);
$req = new TestRequest([
        'id' => 1,
        'name' => "test",
        'lists' => [1,2,3]
    ]
);
$response = $client->test1($req);

var_dump($response);
