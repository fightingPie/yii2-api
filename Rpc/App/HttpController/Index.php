<?php
/**
 * Created by PhpStorm.
 * User: Tioncico
 * Date: 2019/4/11 0011
 * Time: 14:40
 */

namespace App\HttpController;

use App\Utility\Pool\MysqlPool;
use common\models\User;
use EasySwoole\Component\Pool\PoolManager;

class Index extends BaseController
{
    function index()
    {

//        $db = PoolManager::getInstance()->getPool(MysqlPool::class)->getObj();
//        $data = $db->get('user');
//        //使用完毕需要回收
//        PoolManager::getInstance()->getPool(MysqlPool::class)->recycleObj($db);
//        $this->writeJson(2201, json_encode($data), 'success');
        return true;
    }

    function test()
    {
        $this->response()->write('this is test223413423');
        return '/test2';//当执行完test方法之后,返回/test2,让框架继续调度/test2方法
    }

    function test2()
    {
        $this->response()->write('this is test2');
        return true;
    }
}