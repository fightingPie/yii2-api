<?php
/**
 * 路由规则
 */
//基础路由
$baseRuleConfigs = [

];

//api接口路由
$apiRuleConfigs = [
    //例如用户接口
    [
        'controller' => ['v1_api_user' => 'v1/user'],//v1_api_user 为自定义 实际请求 为 http://「domain」/v1_api_user
//                    'except' => ['index'],//禁用预定义方法
        'extraPatterns' => [
            'GET search-user/<user_id:\d+>' => 'search',
            'POST get-token' => 'get-token',
            'GET  reg' => 'reg'
        ],
    ],

];

/**
 * 基础的api url规则配置
 */
$apiUrls = array_map(function ($unit) {
    $urlRule = $unit;
    //防止默认options控制器被屏蔽
    if (isset($unit['only']) && !empty($unit['only']) && !in_array('options', $unit['only'])) {
        $urlRule['only'][] = 'options';
    }
    if (isset($unit['except']) && !empty($unit['except']) && in_array('options', $unit['except'])) {
        $urlRule['except'] = array_merge(array_diff($unit['except'], ['options']));
    }
    //由于ajax设置请求头后,会有一次options请求,默认为所有路由添加支持options请求
    if (isset($unit['extraPatterns']) && !empty($unit['extraPatterns'])) {
        foreach ($unit['extraPatterns'] as $key => $val) {
            if (!is_numeric(strpos($key, 'OPTIONS'))) {
                //判断是否有空格符
                if (is_numeric(strpos($key, ' '))) {
                    //存在
                    $tmp = explode(' ', $key);
                    $k = str_replace($tmp[0], 'OPTIONS', $key);
                    $urlRule['extraPatterns'][$k] = 'options';
                } else {
                    //不存在
                    $urlRule['extraPatterns']['OPTIONS'] = 'options';
                }
            }
        }
    }
    if (isset($unit['patterns']) && !empty($unit['patterns'])) {
        foreach ($unit['patterns'] as $key => $val) {
            if (!is_numeric(strpos($key, 'OPTIONS'))) {
                //判断是否有空格符
                if (is_numeric(strpos($key, ' '))) {
                    //存在
                    $tmp = explode(' ', $key);
                    $k = str_replace($tmp[0], 'OPTIONS', $key);
                    $urlRule['patterns'][$k] = 'options';
                } else {
                    //不存在
                    $urlRule['patterns']['OPTIONS'] = 'options';
                }
            }
        }
    }

    $config = [
        'class' => 'yii\rest\UrlRule',
        'pluralize' => false
    ];
    return array_merge($config, $urlRule);
}, $apiRuleConfigs);

//合并整个项目路由
return array_merge($baseRuleConfigs, $apiUrls);