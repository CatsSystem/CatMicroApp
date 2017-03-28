<?php
/**
 * Created by PhpStorm.
 * User: lidanyang
 * Date: 17/3/1
 * Time: 13:45
 */

namespace app\callback;

use base\Entrance;
use base\server\BaseCallback;
use core\common\Globals;
use core\component\cache\CacheLoader;
use core\component\config\Config;
use core\component\pool\PoolManager;

class MainServer extends BaseCallback
{
    /**
     * 服务启动前执行该回调, 用于添加额外监听端口, 添加额外Process
     * @return mixed
     */
    public function beforeStart()
    {
        // 打开内存Cache进程
        $this->openCacheProcess(function(){
            Globals::setProcessName(Config::getField('project', 'project_name') . 'cache process');
            // 设置全局Server变量
            Globals::$server = \base\server\MainServer::getInstance()->getServer();

            // 初始化连接池
            PoolManager::getInstance()->init('mysql_master');
            PoolManager::getInstance()->init('redis_master');

            // 初始化缓存Cache
            $cache_config = Config::getField('component', 'cache');
            CacheLoader::getInstance()->init(Entrance::$rootPath . $cache_config['cache_path'],
                $cache_config['cache_path']);

            return $cache_config['cache_tick'];
        });
    }

    /**
     * 进程初始化回调, 用于初始化全局变量
     * @param \swoole_websocket_server $server
     * @param $workerId
     */
    public function onWorkerStart($server, $workerId)
    {
        // 加载配置
        Config::load(Entrance::$configPath);

        // 初始化连接池
        PoolManager::getInstance()->init('mysql_master');
        PoolManager::getInstance()->init('redis_master');

        /**
         * 初始化内存缓存
         */
        $cache_config = Config::getField('component', 'cache');
        CacheLoader::getInstance()->init(Entrance::$rootPath . $cache_config['cache_path'],
            $cache_config['cache_path']);
    }

    /**
     * Admin 管理接口, 可自定义管理接口行为
     * @param \swoole_http_request $request
     * @param \swoole_http_response $response
     */
    public function onRequest(\swoole_http_request $request, \swoole_http_response $response)
    {

    }
}