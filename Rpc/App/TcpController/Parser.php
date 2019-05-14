<?php
/**
 * Created by PhpStorm.
 * User: pie
 * Date: 2019/5/14
 * Time: 下午3:07
 */

namespace App\TcpController;
use EasySwoole\Socket\Bean\Caller;
use EasySwoole\Socket\Bean\Response;
use EasySwoole\Socket\AbstractInterface\ParserInterface;
//use EasySwoole\Utility\CommandLine;
class Parser implements ParserInterface
{
    /**
     * @param $raw
     * @param $client
     * @return Caller|null
     */
    public function decode($raw, $client): ?Caller
    {
        $data = substr($raw, '4');
        //为了方便,我们将json字符串作为协议标准
        $data = json_decode($data, true);
        $bean = new Caller();
        $controller = !empty($data['controller']) ? $data['controller'] : 'Index';
        $action = !empty($data['action']) ? $data['action'] : 'index';
        $param = !empty($data['param']) ? $data['param'] : [];
        $controller = "App\\TcpController\\{$controller}";
        $bean->setControllerClass($controller);
        $bean->setAction($action);
        $bean->setArgs($param);
        return $bean;
    }
    /**
     * 只处理pack,json交给控制器
     * encode
     * @param Response $response
     * @param          $client
     * @return string|null
     * @author Tioncico
     * Time: 10:33
     */
    public function encode(Response $response, $client): ?string
    {
        return pack('N', strlen($response->getMessage())) . $response->getMessage();
    }
}