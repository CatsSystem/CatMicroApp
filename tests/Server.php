<?php
/**
 * Created by PhpStorm.
 * User: lidanyang
 * Date: 17/3/15
 * Time: 16:09
 */

require_once "../vendor/autoload.php";

use app\processor\ThriftServiceProcessor;
use Thrift\Factory\TProtocolFactory;
use Thrift\Server\TServerSocket;
use Thrift\Server\TSimpleServer;
use Thrift\Transport\TTransport;

class TTBufferedFactory extends \Thrift\Factory\TTransportFactory
{
    public static function getTransport(TTransport $transport)
    {
        return new \Thrift\Transport\TFramedTransport($transport);
    }
}

class TBinaryProtocolFactory implements TProtocolFactory
{
    private $strictRead_ = false;
    private $strictWrite_ = false;

    public function __construct($strictRead=false, $strictWrite=false)
    {
        $this->strictRead_ = $strictRead;
        $this->strictWrite_ = $strictWrite;
    }

    public function getProtocol($trans)
    {
        return new \Thrift\Protocol\TBinaryProtocolAccelerated($trans, $this->strictRead_, $this->strictWrite_);
    }
}

$service = new app\service\ThriftService();
$processor = new ThriftServiceProcessor($service);
$socket_tranport = new TServerSocket('0.0.0.0', 9503);
$out_factory = $in_factory = new TTBufferedFactory();
$out_protocol = $in_protocol = new TBinaryProtocolFactory(true,true);


//作为cli方式运行，监听端口，官方实现
$transport = new TServerSocket('127.0.0.1', 9503);
$server = new TSimpleServer($processor, $transport, $out_factory, $out_factory, $out_protocol, $out_protocol);
$server->serve();