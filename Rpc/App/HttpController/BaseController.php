<?php
/**
 * Created by PhpStorm.
 * User: pie
 * Date: 2019/5/14
 * Time: 上午11:19
 */

namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;

class BaseController extends Controller
{

    public function index()
    {
        // TODO: Implement index() method.
    }


//    /**
//     * @param \Throwable $throwable
//     */
//    protected function onException(\Throwable $throwable): void
//    {
//        //直接给前端响应500并输出系统繁忙
//        $this->response()->withStatus(Status::CODE_INTERNAL_SERVER_ERROR);
//        $this->response()->write('系统繁忙,请稍后再试 ');
//    }

}