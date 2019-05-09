<?php
/**
 * Api接口基类
 */

namespace api\controllers;

use yii\filters\auth\HttpBearerAuth;
use yii\filters\Cors;
use yii\filters\RateLimiter;
use yii\rest\ActiveController;

class ApiController extends ActiveController
{
    public $modelClass = '';
    public $optional = [
        'get-token',  //认证排除 获取access_token
//        'reg' //认证排除测试注册用户
//        'options'
    ];
    //重写动作
    public $rewriteActions = [
        'update',
        'delete',
        'view',
        'create',
//        'index',
//        'options' //默认支持OPTIONS请求
    ];

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['authenticator']);

        $behaviors['corsFilter'] = [
            'class' => Cors::className(),
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
            ],
        ];

        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
            'optional' => $this->optional,
            'except' => ['options'] //认证排除OPTIONS请求
        ];

        # rate limit部分，速度的设置是在
        #   app\models\User::getRateLimit($request, $action)
        /*  官方文档：
            当速率限制被激活，默认情况下每个响应将包含以下HTTP头发送 目前的速率限制信息：
            X-Rate-Limit-Limit: 同一个时间段所允许的请求的最大数目;
            X-Rate-Limit-Remaining: 在当前时间段内剩余的请求的数量;
            X-Rate-Limit-Reset: 为了得到最大请求数所等待的秒数。
            你可以禁用这些头信息通过配置 yii\filters\RateLimiter::enableRateLimitHeaders 为false, 就像在上面的代码示例所示。
        */
        $behaviors['rateLimiter'] = [
            'class' => RateLimiter::className(),
            'enableRateLimitHeaders' => true,
        ];
        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();
        //判断是否需要重写的控制器
        if (!empty($this->rewriteActions)) {
            foreach ($this->rewriteActions as $actionKey) {
                if (isset($actions[$actionKey]) && $actionKey != 'options') {
                    unset($actions[$actionKey]);
                }
            }
        }
        //设置固定options控制器
        $actions['options'] = [
            'class' => 'yii\rest\OptionsAction',
            // optional:
            'collectionOptions' => ['GET', 'POST', 'HEAD', 'OPTIONS'],
            'resourceOptions' => ['GET', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
        ];
        return $actions;
    }
}