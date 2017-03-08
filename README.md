## 介绍
Swoole Micro 微服务 应用

## 文档

[CatMicro 文档](https://catssystem.gitbooks.io/catdocs/content/micro.html)


## 环境依赖

* [hiredis库](https://github.com/redis/hiredis)
* [thrift库](micro/structure/thrift.md)
* [swoole扩展(>=1.9+, < 2)](https://github.com/swoole/swoole-src)
* [swoole_serialize扩展](https://github.com/swoole/swoole_serialize)
* [phpredis扩展](https://github.com/phpredis/phpredis)
* [hprose-pecl扩展](https://github.com/hprose/hprose-pecl)

## 安装

### Composer安装

```bash
composer create-project --no-dev cat-sys/cat-micro-app {project_name}
```

> 注： 测试阶段请使用 `composer create-project --stability=dev --no-dev cat-sys/cat-micro-app {project_name}`命令安装

## 异步API

### 异步Task

```php
// 实例化异步任务
$task = new AsyncTask('TestTask');
// 发送任务请求
$result = yield $task->test_task(1, "test", [1, 2, 3 ]);
```

### Redis访问

```php
// 获取连接池
$redis_pool = PoolManager::getInstance()->get('redis_master');

// 发起请求
$redis_result = yield $redis_pool->pop()->get('cache');

```

### MySQL访问

```php

// 获取连接池
$mysql_pool = PoolManager::getInstance()->get('mysql_master');

// 发起请求
$sql_result = yield MySQLStatement::prepare()
    ->select("Test",  "*")
    ->limit(0,2)
    ->query($mysql_pool->pop());

```

### Http请求

```php
$http = new Http("www.baidu.com");
yield $http->init();
$result = yield $http->get('/');

```

## 运行

在项目目录下，执行以下命令
```bash
php run.php start
```
进入DEBUG模式。

执行以下命令
```bash
php run.php start -c release
```
指定配置文件目录

## 请求方式

参考`tests`目录中的客户端实现