<?php
/**
 * Created by PhpStorm.
 * User: lidanyang
 * Date: 17/3/4
 * Time: 15:41
 */

namespace app\task;

use core\common\Globals;
use core\component\pool\PoolManager;
use core\component\task\IRunner;
use core\model\MySQLStatement;

class TestTask extends IRunner
{

    private $mysql_pool;

    public function __construct()
    {
        $this->mysql_pool = PoolManager::getInstance()->get('mysql_master');
    }

    public function test_task($id, $name, $arr)
    {
        Globals::var_dump($id);
        Globals::var_dump($name);
        Globals::var_dump($arr);

        $sql_result = MySQLStatement::prepare()
            ->select("Test",  "*")
            ->limit(0,2)
            ->query($this->mysql_pool->pop());
        return $sql_result;
    }
}