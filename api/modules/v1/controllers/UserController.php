<?php

namespace api\modules\v1\controllers;

use api\controllers\ApiController;
use common\models\LoginForm;
use common\models\User;
use yii\base\Request;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\rest\ActiveController;

class UserController extends ApiController
{
    public $modelClass = 'common\models\User';
    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'items',
    ];

    //注册-测试用
    public function actionReg()
    {
        $user = new User();
        $user->generateApiToken();
        $user->setPassword('123456');
        $user->username = 'test';
        $user->save(false);
        return [
            'code' => 0
        ];
    }

    /**
     * 获取 用户获取 access_token
     * @return array
     */
    public function actionGetToken()
    {
        $model = new LoginForm;
        $model->setAttributes(\Yii::$app->request->post());
        if ($user = $model->login()) {
            return $user->api_token;
        } else {
            return $model->errors;
        }
    }


    public function actionSearch()
    {

        $id = \Yii::$app->request->get('user_id');

        return User::findOne($id);
        //分页1
//        $users = [
//            ['id' => 1, 'name' => 'name 1'],
//            ['id' => 2, 'name' => 'name 2'],
//            ['id' => 100, 'name' => 'name 100'],
//            ['id' => 100, 'name' => 'name 100'],
//            ['id' => 100, 'name' => 'name 100'],
//            ['id' => 100, 'name' => 'name 100'],
//            ['id' => 100, 'name' => 'name 100'],
//            ['id' => 100, 'name' => 'name 100'],
//            ['id' => 100, 'name' => 'name 100'],
//        ];
//        return new ArrayDataProvider([
//            'allModels' => $users,
//            'pagination' => [
//                'pageSize' => 2,
//            ],
//            'sort' => [
//                'attributes' => ['id'],
//            ],
//        ]);

        //分页2
//        return new ActiveDataProvider([
//            'query' => User::find(),
//            // 设置分页，比如每页200个条目
//            'pagination' => new Pagination(['pageSize' => 1])
//        ]);
    }
}