<?php

namespace app\service;

use app\processor\HproseServiceIf;
use app\processor\TestRequest;
use app\processor\TestResponse;
use core\common\Globals;
use core\component\client\Http;
use core\component\log\Log;
use core\component\pool\PoolManager;
use core\component\task\AsyncTask;
use core\model\MySQLStatement;

/**
 * Created by PhpStorm.
 * User: lidanyang
 * Date: 17/2/21
 * Time: 15:02
 */

class HproseService implements HproseServiceIf
{

    private $mysql_pool;
    private $redis_pool;

    public function __construct()
    {
        $this->mysql_pool = PoolManager::getInstance()->get('mysql_master');
        $this->redis_pool = PoolManager::getInstance()->get('redis_master');
    }

    /**
     * @param TestRequest $request
     * @return \app\processor\TestResponse
     */
    public function test1(TestRequest $request)
    {
        $response = new TestResponse();


        try{
            // 协程Redis
            $redis_result = yield $this->redis_pool->pop()->get('cache');
            Globals::var_dump($redis_result);

            // 协程MySQL
            $sql_result = yield MySQLStatement::prepare()
                ->select("Test",  "*")
                ->limit(0,2)
                ->query($this->mysql_pool->pop());
            Globals::var_dump($sql_result);

            // 协程Async Task
            $result = yield (new AsyncTask('TestTask'))
                ->test_task(1, "test", [1, 2, 3 ]);
            Globals::var_dump($result);

            // 协程Http
            $http = new Http("www.baidu.com");
            yield $http->init();
            $result = yield $http->get('/');
            Globals::var_dump($result);

            $response->status = 200;
        } catch (\Error $e) {
            Log::DEBUG("Test", $e);
            $response->status = 503;
        }
        return $response;
    }

    /**
     * @param string $name
     * @param int $id
     * @return int
     */
    public function test2($name, $id)
    {
        var_dump("test");
        return $id;
    }

    /**
     * @param TestRequest $request
     * @param int $id
     * @return int
     */
    public function test3(TestRequest $request, $id)
    {
        var_dump("Hprose Service");
        return $id;
    }
}