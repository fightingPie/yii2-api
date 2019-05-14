<?php
/**
 * Created by PhpStorm.
 * User: pie
 * Date: 2019/5/14
 * Time: 下午3:08
 */

namespace App\TcpController;
//use App\Rpc\RpcServer;
use App\Utility\Pool\MysqlPool;
use common\models\User;
use EasySwoole\Component\Pool\PoolManager;
use EasySwoole\EasySwoole\ServerManager;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Socket\AbstractInterface\Controller;

class Index extends Controller{
    function actionNotFound(?string $actionName)
    {
        $this->response()->setMessage("{$actionName} not found \n");
    }
    public function index(){
        $this->response()->setMessage(time());
    }
    public function args()
    {
        $db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj();
        $db->where ("id", 2);
        $data = $db->getOne ("user");
        //使用完毕需要回收
        PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($db);
        $this->response()->setMessage(json_encode($data));

    }

    public function findUser(){
        $args = $this->caller()->getArgs();

        $db = PoolManager::getInstance()->getPool('mysql_test')->getObj();
        $db->where ("id", $args['id']);
        $data = $db->getOne ("user");
        //使用完毕需要回收
        PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($db);
        $this->response()->setMessage(json_encode($data));
    }


    public function delay()
    {
        $client = $this->caller()->getClient();
        TaskManager::async(function ()use($client){
            sleep(1);
            ServerManager::getInstance()->getSwooleServer()->send($client->getFd(),'this is delay message at '.time());
        });
    }
    public function close()
    {
        $this->response()->setMessage('you are goging to close');
        $client = $this->caller()->getClient();
        TaskManager::async(function ()use($client){
            sleep(2);
            ServerManager::getInstance()->getSwooleServer()->send($client->getFd(),'this is delay message at '.time());
        });
    }
    public function who()
    {
        $this->response()->setMessage('you fd is '.$this->caller()->getClient()->getFd());
    }
}