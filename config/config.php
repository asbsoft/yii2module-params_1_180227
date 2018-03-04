<?php

use asb\yii2\common_2_170212\base\UniApplication;


$sublink = 'params';
$adminUrlPrefix = empty(Yii::$app->params['adminPath']) ? '' : Yii::$app->params['adminPath'] . '/';
$type = empty(Yii::$app->type) ? false : Yii::$app->type;

return [
    'models' => [  // shared models: alias => class name or object array
        'Parameter'          => 'asb\yii2\modules\params_1_180227\models\Parameter',
        'ParamsDataProvider' => 'asb\yii2\modules\params_1_180227\models\ParamsDataProvider',
        'ParamsModules'      => 'asb\yii2\modules\params_1_180227\models\ParamsModules',
        'ParamsValues'       => 'asb\yii2\modules\params_1_180227\models\ParamsValues',
        'ParamsValuesQuery'  => 'asb\yii2\modules\params_1_180227\models\ParamsValuesQuery',
    ],
    'routesConfig' => [ // type => prefix|config
        'admin' => $type == UniApplication::APP_TYPE_FRONTEND ? false : [
            'urlPrefix' => $adminUrlPrefix . $sublink,
            'startLink' => [
                'label' => 'Modules parameters manager', // no translate here, it will translate using 'MODULE_UID/module' tr-category
              //'link'  => '', // default
                'action' => 'admin/index',
            ],
        ],
    ],
];
