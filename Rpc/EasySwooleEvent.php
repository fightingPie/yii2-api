<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/5/28
 * Time: 下午6:33
 */

namespace EasySwoole\EasySwoole;


use App\Utility\Pool\MysqlPool;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Socket\Dispatcher;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        // TODO: Implement initialize() method.
        date_default_timezone_set('Asia/Shanghai');

        PoolManager::getInstance()->register(MysqlPool::class, Config::getInstance()->getConf('MYSQL.POOL_MAX_NUM'));

        //多数据库不用新建类  直接调用
        PoolManager::getInstance()->registerAnonymous('mysql_test', function () {
            $conf = Config::getInstance()->getConf("MYSQL_TEST");
            $dbConf = new \EasySwoole\Mysqli\Config($conf);
            return new Mysqli($dbConf);
        });
    }

    /**
     * @param EventRegister $register
     * @throws \EasySwoole\Socket\Exception\Exception
     */
    public static function mainServerCreate(EventRegister $register)
    {

        //tcp

        $server = ServerManager::getInstance()->getSwooleServer();

        $subPort3 = $server->addListener(Config::getInstance()->getConf('MAIN_SERVER.LISTEN_ADDRESS'), 9525,
            SWOOLE_TCP);

        $socketConfig = new \EasySwoole\Socket\Config();
        $socketConfig->setType($socketConfig::TCP);
        $socketConfig->setParser(new \App\TcpController\Parser());
        //设置解析异常时的回调,默认将抛出异常到服务器
        $socketConfig->setOnExceptionHandler(function ($server, $throwable, $raw, $client, $response) {
            echo "tcp服务3  fd:{$client->getFd()} 发送数据异常 \n";
            $server->close($client->getFd());
        });
        /** @var Dispatcher $dispatch */
        $dispatch = new Dispatcher($socketConfig);

        $subPort3->on('receive',
            function (\swoole_server $server, int $fd, int $reactor_id, string $data) use ($dispatch) {
                echo "tcp服务3  fd:{$fd} 发送消息:{$data}\n";
                $dispatch->dispatch($server, $data, $fd, $reactor_id);
            });
        $subPort3->set(
            [
                'open_length_check' => true,
                'package_max_length' => 81920,
                'package_length_type' => 'N',
                'package_length_offset' => 0,
                'package_body_offset' => 4,
            ]
        );
        $subPort3->on('connect', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务3  fd:{$fd} 已连接\n";
        });
        $subPort3->on('close', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "tcp服务3  fd:{$fd} 已关闭\n";
        });

    }

    public static function onRequest(Request $request, Response $response): bool
    {
        // TODO: Implement onRequest() method.
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
        // TODO: Implement afterAction() method.
    }
}