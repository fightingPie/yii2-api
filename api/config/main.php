<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'api\controllers',
    'bootstrap' => ['log'],
    'modules' => [
        'v1' => [
            'class' => 'api\modules\v1\Module',
        ]
    ],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-pai',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                //restful api
                $response = $event->sender;
                $code = $response->getStatusCode();
                $msg = $response->statusText;
                if ($code == 404) {
                    !empty($response->data['message']) && $msg = $response->data['message'];
                }
                //设置固定返回数据参数
                $data = [
                    'code' => $code,
                    'msg' => $msg,
                    'data' => $response->data
                ];
                $code == 200 && $data['data'] = $response->data;
                $response->data = $data;
                $response->format = yii\web\Response::FORMAT_JSON;
            },
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-api', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-api',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => require(__DIR__ . '/url-rules.php')
        ],

    ],
    'params' => $params,
];
